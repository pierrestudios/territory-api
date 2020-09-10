<?php

namespace App\Http\Controllers;

use Auth;
use Gate;
use PDF;
use JWTAuth;
use App\Models\User;
use App\Models\Territory;
use App\Models\Address;
use App\Models\Street;
use App\Models\Note;
use App\Models\Coordinates;
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

    public function index(Request $request, $territoryNum = 1, $nospace = null)
    {
        $territoryArray = $this->getTerritory($territoryNum);
        $territoryArray['space'] = $nospace ? false : true;

        return $this->generatePdf($territoryArray);
    }

    // TODO: Add translation
    public function csv(Request $request, $territoryNum = 1)
    {
        $territoryArray = $this->getTerritory($territoryNum);
        $filename = 'territory-' . $territoryNum . '.csv';
        $headers = [
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $filename,
            'Expires'             => '0',
            'Pragma'              => 'public'
        ];
        $csvCallback = function () use ($territoryNum, $territoryArray) {
            // Create handle
            $handle = fopen('php://output', 'w');

            // Add Headings
            fputcsv($handle, ["Tèritwa  ", "", $territoryNum, " ", "", "", "", "", "", "Frè:"]);

            // Add Columns
            fputcsv(
                $handle, [
                    "Adrès", "Apt", "Dat", "Lang/Nòt", "Semèn", "", "", "Sam", "Dim", "NON AK ENFÒMASYON"
                ]
            );

            foreach ($territoryArray['addresses'] as $street => $addresses) {

                // Add empty Line
                fputcsv($handle, ["", "", "", "", "", "", "", "", "", ""]);

                // Add Street
                fputcsv($handle, [$street, "", "", "", "", "", "", "", "", ""]);

                foreach ($addresses as $inx => $row) {
                    $notes = '';
                    if (count($row['notes']) > 0) {
                        foreach ($row['notes'] as $k => $note) {
                            if ($note['archived'] == 1) {
                                $notes = $note['content'];
                            }
                        }
                    }

                    // Add "Address, Name"
                    fputcsv(
                        $handle, [
                            $row['address'], $row['apt'], "", $notes, "", "", "", "", "", $row['name']
                        ]
                    );
                }
            }
            fclose($handle);
        };

        // Note for stream output: 
        // https://laravel.com/api/5.2/Illuminate/Routing/ResponseFactory.html#method_stream
        return response()->stream($csvCallback, 200, $headers);
    }

    public function map(Request $request, $territoryNum = 1)
    {
        $territoryArray = $this->getTerritory($territoryNum);

        return $this->generateMap($territoryArray);
    }

    public function mapEdit(Request $request, $territoryNum = 1)
    {
        $territoryArray = $this->getTerritory($territoryNum);
        $territoryArray['editable'] = true;

        return $this->generateMap($territoryArray);
    }

    public function mapUpdate(Request $request, $territoryNum)
    {
        $data = $this->updateAddress(
            [
                'id' => $request->input('id'), 
                'lat' => $request->input('lat'), 
                'long' => $request->input('long')
            ]
        );
        return ['data' => $data];
    }

    public function boundaryEdit(Request $request, $territoryNum = 1)
    {
        $territoryArray = $this->getTerritory($territoryNum);
        $territoryArray['editable'] = true;

        return $this->generateBoundaryMap($territoryArray);
    }

    public function boundaryUpdate(Request $request, $territoryNum)
    {
        $territory = Territory::where('number', $territoryNum)->first();
        $data = $this->updateTerritory(
            [
                'id' => $territory->id, 'boundaries' => $request->input('boundaries')
            ]
        );

        return ['data' => $data];
    }

    public function mapBoundaryEdit(Request $request, $territoryNum = 1)
    {
        $territoryArray = $this->getTerritory($territoryNum);
        $territoryArray['editable'] = true;

        return $this->generateMarkersBoundariesMap($territoryArray);
    }

    public function mapBoundaryUpdate(Request $request, $territoryNum)
    {
        if (!empty($request->input('action')) && $request->input('action') == 'update-marker') {
            $data = $this->updateAddress(
                [
                    'id' => $request->input('id'),
                    'lat' => $request->input('lat'),
                    'long' => $request->input('long')
                ]
            );
        } else if (!empty($request->input('action')) && $request->input('action') == 'update-boundary') {
            $territory = Territory::where('number', $territoryNum)->first();
            $data = $this->updateTerritory(
                [
                    'id' => $territory->id,
                    'boundaries' => $request->input('boundaries')
                ]
            );
        } else if (!empty($request->input('action')) && $request->input('action') == 'update-address-territory') {
            $territory = Territory::where('number', $territoryNum)->first();
            $data = (
                $territory->id == $request->input('territoryId') 
                ? false 
                : $this->updateAddress(
                    [
                        'id' => $request->input('id'),
                        'territoryId' => $request->input('territoryId')
                    ]
                )
            );
        }

        return ['data' => $data];
    }

    public function mapMarkersEdit(Request $request, $territoryNum = 1)
    {
        $territoryArray = $this->getTerritory($territoryNum);
        $territoryArray['editable'] = true;

        return $this->generateMarkersBoundariesMap($territoryArray, $markersOnly = true);
    }

    public function boundaryAll(Request $request)
    {
        $terrMapData = [];
        $terrMapData['territories'] = Territory::orderBy('number', 'asc')->get();
        $terrMapData['center'] = [
            'lat' => $terrMapData['territories'][0]->lat,
            'long' => $terrMapData['territories'][0]->lng
        ];

        return view('boundaries-all')->with($terrMapData);
    }
 
    protected function getTerritory($territoryNum)
    {
        $territory = Territory::where('number', $territoryNum)->with(
            [
                'publisher', 'addresses' => function ($query) {
                    $query->where('inactive', '!=', 1)->orderBy('address', 'asc');
                }, 'addresses.street'  => function ($query) {
                    $query->orderBy('street', 'desc');
                }, 'addresses.notes'
            ]
        )->get();

        return [
            'number' => $territory[0]->number,
            'location' => $territory[0]->location,
            'city_state' => $territory[0]->city_state,
            'boundaries' => $territory[0]->boundaries,
            'date' => ($territory[0]->assigned_date != '0000-00-00') 
                ? $territory[0]->assigned_date 
                : date('Y-m-d', time()),
            'total' => count($territory[0]->addresses),
            'publisher' => !empty($territory[0]->publisher) 
                ? $territory[0]->publisher->toArray() 
                : null,
            'addresses' => Coordinates::sortAddressByStreet(
                $territory[0]->addresses->toArray()
            )
        ];
    }

    protected function generatePdf($territoryArray)
    {
        $top = 0;
        $left = 0;
        $orientation = 'portrait';
        $letterhalf = ['width' => 396, 'height' => 612];
        $pdf = PDF::loadView('territory', $territoryArray);
        $pdf->setPaper(
            [
                $left, $top, $letterhalf['width'], $letterhalf['height']
            ], 
            $orientation
        );

        return $pdf->stream();
    }

    protected function generateS13Pdf()
    {
        $top = 0;
        $left = 0;
        $viewTemplate = 'S-13';
        $orientation = 'portrait';
        $letter = ['width' => 612, 'height' => 792];
        $recordsData = $this->compileRecords();
        $pdf = PDF::loadView($viewTemplate, ['recordsData' => $recordsData]);
        $pdf->setPaper(
            [
                $left, $top, $letter['width'], $letter['height']
            ], 
            $orientation
        ); 
        return $pdf->stream();
    }


    protected function generateMap($territoryArray)
    {
        // check if has Coordinates and add it
        if ($territoryArray['addresses']) {
            $buildingCoordinates = [];
            foreach ($territoryArray['addresses'] as $street => $addresses) {
                foreach ($addresses as $i => $address) {
                    if (empty((float)$address['lat']) || empty((float)$address['long'])) {
                        if ($address['street']['is_apt_building']) {
                            if (empty($buildingCoordinates[$address['street']['id']])) {
                                $buildingCoordinates[$address['street']['id']] = Coordinates::getBuildingCoordinates(
                                    $address['street'], $territoryArray['city_state']
                                );
                            }

                            $address['lat'] = $buildingCoordinates[$address['street']['id']]['lat'];
                            $address['long'] = $buildingCoordinates[$address['street']['id']]['long'];
                        } else {
                            $address = Coordinates::getAddessCoordinates(
                                $address, $territoryArray['city_state']
                            );
                        }
                        $territoryArray['addresses'][$street][$i] = $address;

                        // Notes: Store new address coordinates
                        Coordinates::updateAddress($address);
                    }
                }
            }
        }

        return view('map')->with($territoryArray);
    }

    protected function updateTerritory($territoryArray)
    {
        $newTerritory = $this->unTransform($territoryArray, 'territory');
        $territory = Territory::findOrFail($territoryArray['id']);
        return $territory->update($newTerritory);
    }

    protected function updateAddress($addressArray)
    {
        $newAddress = $this->unTransform($addressArray, 'address');
        $address = Address::findOrFail($addressArray['id']);
        return $address->update($newAddress);
    }

    protected function generateBoundaryMap($territoryArray)
    {
        // check if has Coordinates and add it
        if ($territoryArray['addresses']) {
            $terrCoordinates = [];
            $terrCoordinates['number'] = $territoryArray['number'];
            $terrCoordinates['location'] = $territoryArray['location'];
            $terrCoordinates['boundaries'] = $territoryArray['boundaries'];
            foreach ($territoryArray['addresses'] as $street => $addresses) {
                foreach ($addresses as $i => $address) {
                    if (!empty((float)$address['lat']) && !empty((float)$address['long'])) {
                        $terrCoordinates['center'] = ['lat' => $address['lat'], 'long' => $address['long']];
                        break 2;
                    }
                }
            }
        }

        return view('boundary-map')->with($terrCoordinates);
    }

    protected function generateMarkersBoundariesMap($territoryArray, $markersOnly = false)
    {
        // check if has Coordinates and add it
        if ($territoryArray['addresses']) {
            $terrMapData = $buildingCoordinates = [];
            $terrMapData = $territoryArray;
            $terrMapData['territories'] = Territory::orderBy('number', 'asc')->get();
            foreach ($territoryArray['addresses'] as $street => $addresses) {
                foreach ($addresses as $i => $address) {
                    if (empty((float)$address['lat']) || empty((float)$address['long'])) {
                        if ($address['street']['is_apt_building']) {
                            if (empty($buildingCoordinates[$address['street']['id']])) {
                                $buildingCoordinates[$address['street']['id']] = Coordinates::getBuildingCoordinates(
                                    $address['street'], $territoryArray['city_state']
                                );
                            }

                            $address['lat'] = $buildingCoordinates[$address['street']['id']]['lat'];
                            $address['long'] = $buildingCoordinates[$address['street']['id']]['long'];
                        } else {
                            $address = Coordinates::getAddessCoordinates($address, $territoryArray['city_state']);
                        }
                        $territoryArray['addresses'][$street][$i] = $address;

                        // Notes: Store new address coordinates
                        Coordinates::updateAddress($address);
                    }

                    if (empty($terrMapData['center']) && !empty((float)$address['lat']) && !empty((float)$address['long'])) {
                        $terrMapData['center'] = ['lat' => $address['lat'], 'long' => $address['long']];
                    }
                }
            }
        }
        $terrMapData['markersOnly'] = $markersOnly;

        return view('markers-boundary-map')->with($terrMapData);
    }

    protected function createHTMLTable($data, $type)
    {
        $htmlTable = '';
        if (!empty($data)) {
            $htmlTable = '<table width="100%">';
            $rows = '';
            foreach ($data as $k => $v) {
                if ($k == 0) {
                    $rows .= $this->getTableHeaders($v, $type) . $this->getTableRows($v, $type);
                } else {
                    $rows .= $this->getTableRows($v, $type);
                }
            }
            $htmlTable .= $rows . '</table>';
        }

        return $htmlTable;
    }

    protected function getTableHeaders($data, $type)
    {
        $rows = '<tr>';
        foreach ($data as $k => $v) {
            if (in_array($k, self::$tableHeaders[$type])) {
                $rows .= '<th>' . ucfirst($k) . '</th>';
            }
        }
        $rows .= '</tr>';

        return $rows;
    }

    protected function getTableRows($data, $type)
    {
        $rows = '<tr>';
        foreach ($data as $k => $v) {
            if (in_array($k, self::$tableHeaders[$type])) {
                if (is_array($v) && count($v)) {
                    $rows .= '<td>' . $this->createHTMLTable($v, ucfirst($k)) . '</td>';
                } else if (!is_array($v)) {
                    $rows .= '<td>' . $v . '</td>';
                }
            }
        }
        $rows .= '</tr>';

        return $rows;
    }

    protected function compileRecords()
    {
        $dateToLimit = "-12 months";
        $dateSearch = date('Y-m-d', strtotime($dateToLimit));

        // NOTE: Query to get records and join territories (to get Number) and publishers (to get Name)
        if ($result = DB::select(
            "SELECT Terr.number, Pub.first_name, Rec.`id` as RecordId, Pub.`id` as PubId, 
            Pub.last_name, `activity_date`,  `activity_type`, Rec.`created_at` 
            FROM  `records` Rec
            LEFT JOIN territories Terr ON Rec.`territory_id` = Terr.`id` 
            LEFT JOIN publishers Pub ON Rec.`publisher_id`  = Pub.`id` 
            WHERE Rec.`created_at` > '" . $dateSearch . "' 
            ORDER BY number, Rec.`created_at`"
        )
        ) { 
            // Note: LIMIT to test this query
            // LIMIT 300 --- AND Terr.number = '1' 

            // var_dump($result); exit; 

            // NOTE: store territory Numbers in $numbers var
            $numbers = [];

            // NOTE: store territory records by Number in $numbersData var
            $numbersData = [];

            // NOTE: store all territory records by Number in $recordsData var
            $recordsData = [];

            // NOTE: store previous record to "match" with checkin/checkout
            $match = null;

            // NOTE: store unmatched $row in $unmatched var
            $unmatched = [];

            // NOTE: loop thru each entry to get the record info
            foreach ($result as $inx => $obj) {
                $row = (array)$obj;

                // NOTE: check if the territory number is already stored, if not, Start new entry for Number
                if ($row['number'] && empty($numbers[$row['number']])) {

                    // NOTE: store new Number in array $numbers
                    $numbers[$row['number']] = $row['number'];
                }

                // NOTE: check to see if We have a $match Publisher ('PubId'), then find 'checkout'
                if (!empty($match) && $match['PubId'] == $row['PubId'] && $match['number'] == $row['number']) {

                    // NOTE: get matched record, then store new record for territory Number
                    $matchedRecord = $this->getMatchedRecord($match, $row);
                    if (!empty($matchedRecord)) {
                        if (empty($numbersData[$row['number']])) { $numbersData[$row['number']] = ['records' => []];
                        }

                        array_push($numbersData[$row['number']]['records'], $matchedRecord);
                    }

                    // if $match 'checkout' has a 'checkin', we reset $match
                    if ($row['activity_type'] == 'checkin') { $match = null;
                    }

                    // If $match is not same publisher as current row   
                } else {

                    // Store the unmatched
                    if (!empty($match)) {
                        $unmatchedRecord = $this->getUnmatchedRecord($match);
                        if (!empty($unmatchedRecord)) {
                            if (empty($numbersData[$row['number']])) {
                                $numbersData[$row['number']] = ['records' => []];
                            }
                            array_push($numbersData[$row['number']]['records'], $unmatchedRecord);
                        }
                    }

                    // Make current row the new $matched
                    $match = $row;

                    // If this is the last entry for the territory Number, output it and reset $match
                    if (empty($result[$inx + 1]) || empty($numbers[$result[$inx + 1]->number])) {
                        $unmatchedRecord = $this->getUnmatchedRecord($match);
                        if (!empty($unmatchedRecord)) {
                            if (empty($numbersData[$row['number']])) {
                                $numbersData[$row['number']] = ['records' => []];
                            }
                            array_push($numbersData[$row['number']]['records'], $unmatchedRecord);
                        }
                        $match = null;
                    }
                }
            }

            // Sort thru each $numbersData
            foreach ($numbersData as $number => $data) {
                array_push($recordsData, new TerritoryRecordData($number, $data));
            }

            return $recordsData;
        }

        return [];
    }

    protected function getMatchedRecord($matched, $row)
    {
        $name = $matched['first_name'] . ' ' . $matched['last_name'];
        $checkin = '';
        $checkout = '';

        if ($matched['activity_type'] == 'checkout') {
            $checkout = $matched['activity_date'];
        } else if ($row['activity_type'] == 'checkout') {
            $checkout = $row['activity_date'];
        }

        if ($row['activity_type'] == 'checkin') {
            $checkin = $row['activity_date'];
        } else if ($matched['activity_type'] == 'checkin' && strtotime($matched['activity_date']) > strtotime($row['activity_date'])) { // Compare Date instead -- $matched['RecordId'] > $row['RecordId']
            $checkin = $matched['activity_date'];
        }

        return new RecordEntry(
            [
            'publisher' => $name,
            'checkin' => $checkin,
            'checkout' => $checkout
            ]
        );
    }

    protected function getUnmatch($number, $unmatched)
    {
        $unmatches = [];
        foreach ($unmatched as $k => $v) {
            if ($v['number'] == $number) {
                $unmatches[] = $v;
            }
        }
        return $unmatches;
    }

    protected function getUnmatchedRecord($unmatch)
    {
        // Get ONLY "checkouts"
        if ($unmatch['activity_type'] == 'checkin') {
            return;
        }

        $name = $unmatch['first_name'] . ' ' . $unmatch['last_name'];
        $checkin = '';
        $checkout = '';

        if ($unmatch['activity_type'] == 'checkout') {
            $checkout = $unmatch['activity_date'];
        }

        return new RecordEntry(
            [
                'publisher' => $name,
                'checkin' => $checkin,
                'checkout' => $checkout
            ]
        );
    }
}


class RecordEntry
{
    public $publisher;
    public $checkin;
    public $checkout;

    public function __construct($data)
    {
        if ($data['publisher']) {
            $this->publisher = $data['publisher'];
        }

        if ($data['checkin']) {
            $this->checkin = $data['checkin'];
        }

        if ($data['checkout']) {
            $this->checkout = $data['checkout'];
        }
    }
}

class TerritoryRecordData
{
    public $number;
    public $records;

    public function __construct($number, $data)
    {
        $this->number = $number;
        if ($data['records']) {
            $this->records = $data['records'];
        }
    }
}
