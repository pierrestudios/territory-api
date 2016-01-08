<?php

namespace App\Http\Controllers;

use Auth;
use Gate;
use PDF;
use JWTAuth;
use App\User;
use App\Territory;
use App\Address;
use App\Note;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;

class PrintController extends ApiController
{    
	
	static $tableHeaders = [
		'Addresses' => ['name', 'phone', 'address', 'notes'],
		'Notes' => ['date', 'content']
	];
	
   	public function index(Request $request) {
/*
		if ( ! $this->hasAccess($request) ) {
			return Response()->json(['error' => 'Access denied.'], 500);
		}
*/

		return $this->territory($request, 4);

		$pdf = \App::make('dompdf.wrapper');
		
		$pdf->loadHTML('<h1>Test</h1>');
		return $pdf->stream();
		
		return ['data' => ''];
   	}
   	
   	public function template(Request $request, $territoryId = 4) {
		$territory = Territory::where('id', $territoryId)->with(['publisher', 'addresses' => function ($query) {
		    $query->where('inactive', null);
		}, 'addresses.notes' => function ($query) {
		    $query->orderBy('date', 'desc');
		}])->get();  
		// dd($territory);  
		// $territoryHtmlTable = $this->createHTMLTable($territory[0]->addresses->toArray(), 'Addresses');

		return view('territory')->with([
			// 'table' => $territoryHtmlTable, 
			'number' => $territory[0]->number,
			'location' => $territory[0]->location,
		]);
   	}
   	
   	public function territory(Request $request, $territoryId) {
/*
		if ( ! $this->hasAccess($request) ) {
			return Response()->json(['error' => 'Access denied.'], 500);
		}
*/

		$territory = Territory::where('id', $territoryId)->with(['publisher', 'addresses' => function ($query) {
		    $query->where('inactive', null);
		}, 'addresses.notes' => function ($query) {
		    $query->orderBy('date', 'desc');
		}])->get(); //toArray();
		// dd($territory); 
		
		$territoryHtmlTable = '<h1>Number '. $territory[0]->number .'</h1>';
		$territoryHtmlTable .= '<h3>Location '. $territory[0]->location .'</h3>'; 
		$territoryHtmlTable .= $this->createHTMLTable($territory[0]->addresses->toArray(), 'Addresses');
		// dd($territoryHtmlTable);
		
        // $territoryPdf = PDF::loadView('pdf.sample',array('table' => $territoryHtmlTable));
        // $territoryPdfFile = '/territories/territory_' . $territory[0]->number . '.pdf';
		// $territoryPdf->save(storage_path() . $territoryPdfFile);


		// $pdf = \App::make('dompdf.wrapper');
		$pdf = PDF::loadView('territory', [
			'table' => $territoryHtmlTable, 
			'number' => $territory[0]->number,
			'location' => $territory[0]->location,
		]);
		$pdf->setPaper(array(0, 0, 396, 612), 'portrait'); // Letterhalf
		// array(0, 0, 306, 396) = .36 of Letterhalf 
		// array(0, 0, 612, 792) = .36 of Letter = 1700 pixels x 2200 pixels
		/*
at 200 dpi:

A4 = 1654 pixels x 2339 pixels
A5 = 1165 pixels x 1654 pixels
Executive = 1450 pixels x 2100 px
Letter = 1700 pixels x 2200 pixels
Legal = 1700 pixels x 2800 pixels	
		*/	
		// $dompdf->set_paper(array(0, 0, 595, 841), 'portrait');
		// $pdf->setPaper("Letter", 'landscape');
		// $pdf->loadHTML($territoryHtmlTable);
		return $pdf->stream(); 
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
}

