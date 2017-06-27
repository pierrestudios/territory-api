<?php

namespace App\Http\Controllers;

use Auth;
use Gate;
use PDF;
use JWTAuth;
use App\User;
use App\Territory;
use App\Address;
use App\Street;
use App\Note;
use App\Coordinates;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\DB;

class PrintController extends ApiController
{    
	
	static $tableHeaders = [
		'Addresses' => ['name', 'phone', 'address', 'notes'],
		'Notes' => ['date', 'content']
	];
	
   	public function index(Request $request, $territoryNum = 1, $nospace = null) {
/*
		if ( ! $this->hasAccess($request) ) {
			return Response()->json(['error' => 'Access denied.'], 500);
		}
*/

		$territoryArray = $this->getTerritory($territoryNum);
		
		// Add space after each street list?
		$territoryArray['space'] = $nospace ? false : true;
		
		return $this->generatePdf($territoryArray);

   	}
   	
   	public function csv(Request $request, $territoryNum = 1) {
/*
		if ( ! $this->hasAccess($request) ) {
			return Response()->json(['error' => 'Access denied.'], 500);
		}
*/
		ini_set('display_errors', 1);
		ini_set('error_reporting', E_ALL);

		$territoryArray = $this->getTerritory($territoryNum);
 
	    $filename = 'territory-'. $territoryNum . '.csv';	    
	    
	    $headers = [
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $filename,
            'Expires'             => '0',
            'Pragma'              => 'public'
	    ];
		
		 
	   $callback = function() use ($territoryNum, $territoryArray) {
	        $handle = fopen('php://output', 'w');
	        
	        // Heading
			fputcsv($handle, ["Tèritwa  ", "", $territoryNum, " ", "", "", "", "", "", "Frè:"]);
			
			// Columns
			fputcsv($handle, ["Adrès","Apt","Dat","Lang/Nòt","Semèn", "", "","Sam","Dim","NON AK ENFÒMASYON"]);
			
			
	        foreach ($territoryArray['addresses'] as $street => $addresses) { 
		        
		        // Line
				fputcsv($handle, ["","","","","","","","","",""]);
				
				// Street
				fputcsv($handle, [$street,"","","","","","","","",""]);
			
		        foreach ($addresses as $inx => $row) {
					$notes = '';
					if (count($row['notes']) > 0) {
						foreach ($row['notes'] as $k => $note) {
							if ($note['archived'] == 1)
								$notes = $note['content'];
						}		
					}
			        // Address, Name
					fputcsv($handle, [$row['address'],$row['apt'],"",$notes,"","","","","",$row['name']]);
	            }
	        }
	        fclose($handle);
	    };
	    // dd($callback);
 
		// https://laravel.com/api/5.2/Illuminate/Routing/ResponseFactory.html#method_stream
		return response()->stream($callback, 200, $headers); 
		// return ['data' => $territoryArray];
	}
   
   	public function map(Request $request, $territoryNum = 1) {
/*
		if ( ! $this->hasAccess($request) ) {
			return Response()->json(['error' => 'Access denied.'], 500);
		}
*/
		// \DB::enableQueryLog();
		
		$territoryArray = $this->getTerritory($territoryNum);
		
		// dd(\DB::getQueryLog());
		// dd($territoryArray);
		return $this->generateMap($territoryArray);

   	}
   	
   	public function mapEdit(Request $request, $territoryNum = 1) {
/*
		if ( ! $this->hasAccess($request) ) {
			return Response()->json(['error' => 'Access denied.'], 500);
		}
*/

		$territoryArray = $this->getTerritory($territoryNum);
		
		$territoryArray['editable'] = true;
		
		return $this->generateMap($territoryArray);

   	}
   	
   	public function mapUpdate(Request $request, $territoryNum) {
/*
		if ( ! $this->hasAccess($request) ) {
			return Response()->json(['error' => 'Access denied.'], 500);
		}
*/
		
		$data = $this->updateAddress(['id' => $request->input('id'), 'lat' => $request->input('lat'), 'long' => $request->input('long')]);
		return ['data' => $data];

   	}
   	   	
   	public function boundaryEdit(Request $request, $territoryNum = 1) {
/*
		if ( ! $this->hasAccess($request) ) {
			return Response()->json(['error' => 'Access denied.'], 500);
		}
*/

		$territoryArray = $this->getTerritory($territoryNum);
		
		$territoryArray['editable'] = true;
		
		return $this->generateBoundaryMap($territoryArray);

   	}
   	
   	public function boundaryUpdate(Request $request, $territoryNum) {
/*
		if ( ! $this->hasAccess($request) ) {
			return Response()->json(['error' => 'Access denied.'], 500);
		}
*/
		$territory = Territory::where('number', $territoryNum)->first();
		$data = $this->updateTerritory(['id' => $territory->id, 'boundaries' => $request->input('boundaries')]);
		return ['data' => $data];

   	}

   	public function mapBoundaryEdit(Request $request, $territoryNum = 1) {
/*
		if ( ! $this->hasAccess($request) ) {
			return Response()->json(['error' => 'Access denied.'], 500);
		}
*/

		$territoryArray = $this->getTerritory($territoryNum);
		
		$territoryArray['editable'] = true;
		
		return $this->generateMarkersBoundariesMap($territoryArray);

   	}
   	
   	public function mapBoundaryUpdate(Request $request, $territoryNum) {
/*
		if ( ! $this->hasAccess($request) ) {
			return Response()->json(['error' => 'Access denied.'], 500);
		}
*/
		if(!empty($request->input('action')) && $request->input('action') == 'update-marker') {
			$data = $this->updateAddress(['id' => $request->input('id'), 'lat' => $request->input('lat'), 'long' => $request->input('long')]);
		} else if(!empty($request->input('action')) && $request->input('action') == 'update-boundary') {
			$territory = Territory::where('number', $territoryNum)->first();
			$data = $this->updateTerritory(['id' => $territory->id, 'boundaries' => $request->input('boundaries')]);
		} else if(!empty($request->input('action')) && $request->input('action') == 'update-address-territory') {
			$territory = Territory::where('number', $territoryNum)->first();
			$data = ($territory->id==$request->input('territoryId') ? false : $this->updateAddress(['id' => $request->input('id'), 'territoryId' => $request->input('territoryId')]) );
		}
		return ['data' => $data];

   	}
   	   	
   	public function boundaryAll(Request $request) {
/*
		if ( ! $this->hasAccess($request) ) {
			return Response()->json(['error' => 'Access denied.'], 500);
		}
*/
		$terrMapData = [];
		$terrMapData['territories'] = Territory::orderBy('number', 'asc')->get();
		// dd($terrMapData['territories']);
		$terrMapData['center'] = ['lat' => $terrMapData['territories'][0]->lat, 'long' => $terrMapData['territories'][0]->lng];
		  
		return view('boundaries-all')->with($terrMapData);
		
   	}
   	
   	public function generateIcon($string) {
	   
	    $font = 'arial';
	 
	    // unfortunately we still must do some offsetting
	     switch (ord(substr($string,0,1))) {
	         case 49: //1
	            $offset = -2;
	            break;
	         case 55: //7
	            $offset = -1;
	            break;
	         case 65: //A
	            $offset = 1;
	            break;
	         case 74: //J
	            $offset = -1;
	            break;
	         case 84: //T
	            $offset = 1;
	            break;
	         case 99: //c
	            $offset = -1;
	            break;
	         case 106: //j
	            $offset = 1;
	            break;
	     }
	     if (strlen($string) == 1) {
	        $fontsize = 10.5;
	     } else if (strlen($string) == 2) {
	        $fontsize = 9;
	     } else {
	        $fontsize = 10.5;
	        $offset = 0; //reset offset
	        $string = chr(149);
	     }
	 
	     $bbox = imagettfbbox($fontsize, 0, $font, $string);
	     $width = $bbox[2] - $bbox[0] + 1;
	     $height = $bbox[1] - $bbox[7] + 1;
	 
	     $image_name = '';// "http://chart.apis.google.com/chart?cht=mm&chs=20x34&chco=$color,$color,000000&ext=.png";
	     $im = imagecreatefrompng($image_name);
	     imageAlphaBlending($im, true);
	     imageSaveAlpha($im, true);
	     $black = imagecolorallocate($im, 0, 0, 0);
	 
	     imagettftext($im, $fontsize, 0, 11 - $width/2 + $offset, 9 + $height/2, $black, $font, $string);
	 
	     header("Content-type: image/png");
	     imagepng($im);
	     imagedestroy($im);
   	}
   	
   	
   	public static function performDBBackup() {
	   	$output = array();
		$return_var = -1;
		$dbn = env('DB_DATABASE');
		$dbu = env('DB_USERNAME');
		$dbp = env('DB_PASSWORD');
		$command='mysqldump --user='.$dbu.' --password='.$dbp.' --host=localhost '.$dbn.' > /var/www/html/APPS/territory-api/database/backups/db-backup-'.date('m-g-Y', time()).'.sql';
		$last_line = exec($command, $output, $return_var);
		/*
		SSH::run($commands, function($line) {
		    echo $line.PHP_EOL;
		});
		*/
		
		if ($return_var === 0) {
			// success
    	} else {
			// fail or other exceptions
			var_dump($output);
    	}
   	}
   	
   	public function hf() {
	   	$pdf = PDF::loadView('header-footer');
		return $pdf->stream();
   	}
   	
   	public function template(Request $request, $territoryNum = 1, $nospace = null) {
/*
		if ( ! $this->hasAccess($request) ) {
			return Response()->json(['error' => 'Access denied.'], 500);
		}
*/
		$territoryArray = $this->getTerritory($territoryNum);
		$territoryArray['space'] = $nospace;
		return view('territory')->with($territoryArray);
   	}
   	
   	protected function getTerritory($territoryNum) {
		$territory = Territory::where('number', $territoryNum)->with(['publisher', 'addresses' => function ($query) {
		    $query->where('inactive', '!=', 1)->orderBy('address', 'asc');
		}, 'addresses.street'  => function ($query) {
		    $query->orderBy('street', 'desc');
		}, 'addresses.notes'])->get();  //toArray();
		// dd($territory); 
		
		return [
			'number' => $territory[0]->number,
			'location' => $territory[0]->location,
			'city_state' => $territory[0]->city_state,
			'boundaries' => $territory[0]->boundaries,
			'date' => ($territory[0]->assigned_date != '0000-00-00') ? $territory[0]->assigned_date : date('Y-m-d', time()),
			'total' => count($territory[0]->addresses),
			'publisher' => !empty($territory[0]->publisher) ? $territory[0]->publisher->toArray() : null,
			'addresses' => Coordinates::sortAddressByStreet($territory[0]->addresses->toArray())
		];
   	}
   	
   	protected function generatePdf($territoryArray) {
		$pdf = PDF::loadView('territory', $territoryArray);
		$pdf->setPaper(array(0, 0, 396, 612), 'portrait'); // Letterhalf
		return $pdf->stream();
   	}

    protected function generateS13Pdf() {
	    $recordsData = $this->compileRecords();	    
	    
	   	// return view('S-13')->with(['recordsData' => $recordsData]);
	   	
		$pdf = PDF::loadView('S-13', ['recordsData' => $recordsData]);
		$pdf->setPaper(array(0, 0, 612, 792), 'portrait'); // Letter 
		return $pdf->stream();
   	}   
   	
   	
   	protected function generateMap($territoryArray) {
	   	// check if has Coordinates and add it
	   	if($territoryArray['addresses']) {
		   	$buildingCoordinates = [];
	   		foreach($territoryArray['addresses'] as $street => $addresses) {
		   		foreach($addresses as $i => $address) {
			   		// dd($address);
			   		
		   			if(empty((float)$address['lat']) || empty((float)$address['long'])) {  
		   				if($address['street']['is_apt_building']) {
			   				if(empty($buildingCoordinates[$address['street']['id']])) 
				   				$buildingCoordinates[$address['street']['id']] = Coordinates::getBuildingCoordinates($address['street'], $territoryArray['city_state']);

			   				$address['lat'] = $buildingCoordinates[$address['street']['id']]['lat'];
				   			$address['long'] = $buildingCoordinates[$address['street']['id']]['long'];
		   				} else {
			   				$address = Coordinates::getAddessCoordinates($address, $territoryArray['city_state']);
		   				}
		   				// dd($address);
		   				$territoryArray['addresses'][$street][$i] = $address;
		   				
		   				// Store in db
		   				Coordinates::updateAddress($address);
		   			}	
		   		}		
	   		}
	   	}	 
		return view('map')->with($territoryArray);
	}	
	
	protected function updateTerritory($territoryArray) {
		$newTerritory = $this->unTransform($territoryArray, 'territory');
        $territory = Territory::findOrFail($territoryArray['id']);
        return $territory->update($newTerritory);
	}
	
	protected function updateAddress($addressArray) {
		$newAddress = $this->unTransform($addressArray, 'address');
        $address = Address::findOrFail($addressArray['id']);
        return $address->update($newAddress);
	}	
	
	protected function generateBoundaryMap($territoryArray) {
	   	// check if has Coordinates and add it
	   	if($territoryArray['addresses']) {
		   	$terrCoordinates = [];
		   	$terrCoordinates['number'] = $territoryArray['number'];
		   	$terrCoordinates['location'] = $territoryArray['location'];
		   	$terrCoordinates['boundaries'] = $territoryArray['boundaries'];
	   		foreach($territoryArray['addresses'] as $street => $addresses) {
		   		foreach($addresses as $i => $address) {
			   		// dd($address);
			   		
		   			if(!empty((float)$address['lat']) && !empty((float)$address['long'])) {  
		   				$terrCoordinates['center'] = ['lat' => $address['lat'], 'long' => $address['long']]; 
		   				break 2;
		   			}	
		   		}		
	   		}
	   	}	 
		return view('boundary-map')->with($terrCoordinates);
	}
	
	protected function generateMarkersBoundariesMap($territoryArray) {
	   	// check if has Coordinates and add it
	   	if($territoryArray['addresses']) {
		   	$terrMapData = $buildingCoordinates = [];
		   	$terrMapData = $territoryArray;
		   	$terrMapData['territories'] = Territory::orderBy('number', 'asc')->get();
		   	// dd($terrMapData['territories']);
	   		foreach($territoryArray['addresses'] as $street => $addresses) {
		   		foreach($addresses as $i => $address) {
			   		// dd($address);
			   		
			   		if(empty((float)$address['lat']) || empty((float)$address['long'])) {  
		   				if($address['street']['is_apt_building']) {
			   				if(empty($buildingCoordinates[$address['street']['id']])) 
				   				$buildingCoordinates[$address['street']['id']] = Coordinates::getBuildingCoordinates($address['street'], $territoryArray['city_state']);

			   				$address['lat'] = $buildingCoordinates[$address['street']['id']]['lat'];
				   			$address['long'] = $buildingCoordinates[$address['street']['id']]['long'];
		   				} else {
			   				$address = Coordinates::getAddessCoordinates($address, $territoryArray['city_state']);
		   				}
		   				// dd($address);
		   				$territoryArray['addresses'][$street][$i] = $address;
		   				
		   				// Store in db
		   				Coordinates::updateAddress($address);
		   			}
		   			
		   			if(empty($terrMapData['center']) && !empty((float)$address['lat']) && !empty((float)$address['long'])) {  
		   				$terrMapData['center'] = ['lat' => $address['lat'], 'long' => $address['long']];
		   			}	
		   		}		
	   		}
	   	}	 
		return view('markers-boundary-map')->with($terrMapData);
	}
   	
   	protected function createHTMLTable($data, $type) {
	   	// return '<h1>Test</h1>';
	   	// dd($data);
	   	$htmlTable = '';
	   	if(!empty($data)) {
		   	$htmlTable = '<table width="100%">';
		   	$rows = '';
		   	foreach($data as $k => $v) {
			   	if($k == 0) $rows .= $this->getTableHeaders($v, $type) . $this->getTableRows($v, $type);
			   	else $rows .= $this->getTableRows($v, $type);
		   	}
		   	$htmlTable .= $rows . '</table>';
	   	}
	   	return $htmlTable; 
   	}
   	
   	protected function getTableHeaders($data, $type) {
	   	$rows = '<tr>';
	   	foreach($data as $k => $v) {
		   	if (in_array($k, self::$tableHeaders[$type])) 
		   		$rows .= '<th>'. ucfirst($k) .'</th>'; 
	   	}
	   	$rows .= '</tr>';
	   	return $rows;
   	}
   	
   	protected function getTableRows($data, $type) {
	   	// echo ' $type ' . $type;
	   	$rows = '<tr>';
	   	foreach($data as $k => $v) {
		   	if (in_array($k, self::$tableHeaders[$type])) {
			   	if (is_array($v) && count($v)) // dd($v); // dd($this->createHTMLTable($v, ucfirst($k)));
			   		$rows .= '<td>'. $this->createHTMLTable($v, ucfirst($k)) .'</td>';
			   	else if (!is_array($v)) $rows .= '<td>'. $v .'</td>';	
		   	} 
	   	}
	   	$rows .= '</tr>';
	   	return $rows;
   	}
   	
   	protected function compileRecords() { 
		// NOTE: Date to limit query (default 12 months back)
		$dateSearch = date('Y-m-d', strtotime("-12 months"));
			
		// NOTE: Query to get records and join territories (to get Number) and publishers (to get Name)
		if ($result = DB::select("SELECT Terr.number, Pub.first_name, Rec.`id` as RecordId, Pub.`id` as PubId, Pub.last_name , `activity_date` ,  `activity_type` , Rec.`created_at` 
FROM  `records` Rec
LEFT JOIN territories Terr ON Rec.`territory_id` = Terr.`id` 
LEFT JOIN publishers Pub ON Rec.`publisher_id`  = Pub.`id` 
WHERE Rec.`created_at` > '". $dateSearch ."'
ORDER BY number, Rec.`created_at` 
")) {
	
			// var_dump($result); exit;

			// NOTE: store territory Numbers in $numbers var
			$numbers = [];
			
			// NOTE: store territory records by Number in $numbersData var
			$numbersData = [];
			
			// NOTE: store all territory records by Number in $recordsData var
			$recordsData = [];
			
			// NOTE: store previous record to "match" with checkin/checkout
			$matched = null;
			
			// NOTE: loop thru each entry to get the record info
			foreach($result as $inx => $obj) {
				$row = (array)$obj;
				
				// NOTE: check if the territory number is already stored, if not, Start new entry for Number
				if ($row['number'] && empty($numbers[$row['number']])) {
					
					// NOTE: store new Number in array $numbers
					$numbers[$row['number']] = $row['number'];
				}
				
				// NOTE: check to see if We have a $matched Publisher ('PubId'), then find 'checkout'
				if (!empty($matched) && $matched['PubId'] == $row['PubId']) { 

					// NOTE: get matched record, then store new record for territory Number
					$matchedRecord = $this->getMatchedRecord($matched, $row);
					if (!empty($matchedRecord)) {
						if (empty($numbersData[$row['number']])) $numbersData[$row['number']] = ['records' => []];
						
						array_push($numbersData[$row['number']]['records'], $matchedRecord);
					}
					
					// if $matched 'checkout' has a 'checkin', we reset $matched
					if ($row['activity_type'] == 'checkin') $matched = null;
					
				// If $matched is not same publisher as current row	
				} else {
					
					// Make current row the new $matched
					$matched = $row;
					
					// If this is the last entry for the territory Number, output it and reset $matched
					if (empty($result[$inx+1]) || empty($numbers[$result[$inx+1]->number])) {
						$matched = null;
					}
				}
			}
		    
			foreach($numbersData as $number => $data) 
				array_push($recordsData, new TerritoryRecordData($number, $data));
			
			// var_dump($recordsData); exit;
			return $recordsData;
		}
		
		return [];
	}
	
	protected function getMatchedRecord($matched, $row) {		
		$name = $matched['first_name'] . ' ' . $matched['last_name'];
		$checkin = '';
		$checkout = '';
		
		if ($matched['activity_type'] == 'checkout') 
			$checkout = $matched['activity_date']; 
		else if ($row['activity_type'] == 'checkout')
			$checkout = $row['activity_date'];
		
		if ($row['activity_type'] == 'checkin')
			$checkin = $row['activity_date'];
		else if ($matched['activity_type'] == 'checkin' && $matched['RecordId'] > $row['RecordId'])
			$checkin = $matched['activity_date'];
		
		return new RecordEntry([
			'publisher' => $name,
			'checkin' => $checkin,
			'checkout' => $checkout
		]);
	}
}


class RecordEntry {
	public $publisher;
	public $checkin;
	public $checkout;
	
	public function __construct($data) {
		if ($data['publisher']) $this->publisher = $data['publisher'];
		if ($data['checkin']) $this->checkin = $data['checkin'];
		if ($data['checkout']) $this->checkout = $data['checkout'];
	}
}

class TerritoryRecordData {
	public $number;
	public $records;
	
	public function __construct($number, $data) {
		$this->number = $number;
		if ($data['records']) $this->records = $data['records'];
	}
}
