<?php
namespace App\Http\Controllers;

use Auth;
use DB;
use Gate;
use JWTAuth;
use Log;
use App\Models\User;
use App\Models\Publisher;
use App\Models\Territory;
use App\Models\Address;
use App\Models\Street;
use App\Models\Note;
use App\Models\Phone;
use App\Models\Record;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;

class TerritoriesController extends ApiController
{
    public function index(Request $request)
    {
        if (!$this->hasAccess($request)) {
            return Response()->json(['error' => 'Access denied.'], 401);
        }

        return [
            'data' => $this->transformCollection(
                Territory::latest()->with(['publisher'])->get(),
                'territory'
            )
        ];
    }

    public function filter(Request $request)
    {
        if (!$this->hasAccess($request)) {
            return Response()->json(['error' => 'Access denied.'], 401);
        }

        return [
            'data' => $this->transformCollection(
                Territory::latest()
                    ->where(Territory::applyFilters($request->all()))
                    ->get(),
                'territory'
            )
        ];
    }

    public function availables(Request $request)
    {
        if (!$this->hasAccess($request)) {
            return Response()->json(['error' => 'Access denied.'], 401);
        }

        return [
            'data' => $this->transformCollection(
                Territory::latest()
                    ->where('publisher_id', null)
                    ->get(), 'territory'
            )
        ];
    }

    public function view(Request $request, $territoryId = null)
    {
        if (!$this->hasAccess($request)) {
            return Response()->json(['error' => 'Access denied.'], 401);
        }

        try {
            // Note: Get ONLY 4 Months of notes
            $fromDate = date('Y-m-d', strtotime("-4 months"));
            $isDBSqlite = DB::connection()->getDriverName() == 'sqlite';
            $territory = Territory::where('id', $territoryId)->with(
                [
                    'publisher', 'addresses' => function ($query) {
                        $query->where('inactive', '!=', 1)
                            ->orderBy('address', 'asc');
                    }, 'addresses.street', 'addresses.phones.notes' => function ($query) use ($fromDate, $isDBSqlite) {
                        $query->whereRaw(
                            DB::raw("symbol IN (" .
                                Phone::STATUS_NOT_CURRENT_LANGUAGE . "," .
                                Phone::STATUS_NOT_IN_SERVICE . "," .
                                Phone::STATUS_DO_NOT_CALL .
                            ") OR " . 

                            // Note: Use alternative fn for sqlite
                            ($isDBSqlite ? "DATE" : "STR_TO_DATE") .
                            "(date, '%Y-%m-%d') > '" . $fromDate . "'")
                        )
                            ->orderBy('created_at', 'desc');
                    }, 'addresses.notes' => function ($query) use ($fromDate, $isDBSqlite) {
                        $query->whereRaw(
                            DB::raw("archived = '1' OR " .

                            // Note: Use alternative fn for sqlite
                            ($isDBSqlite ? "DATE" : "STR_TO_DATE") .
                            "(date, '%Y-%m-%d') > '" . $fromDate . "'")
                        )
                            ->orderBy('archived', 'desc')
                            ->orderBy('date', 'desc')
                            ->orderBy('created_at', 'desc');
                    }
                ]
            )
                ->get();

            $data = !empty($territory[0]) ? $this->transform($territory[0]->toArray(), 'territory') : null;
        } catch (Exception $e) {
            $data = ['error' => 'Territory not found', 'message' => $e->getMessage()];
        }
        return ['data' => $data];
    }

    public function viewWithInactives(Request $request, $territoryId = null)
    {
        if (!$this->hasAccess($request)) {
            return Response()->json(['error' => 'Access denied.'], 401);
        }

        try {
            // Note: Get ONLY 12 Months of notes
            $fromDate = date('Y-m-d', strtotime("-12 months"));
            $isDBSqlite = DB::connection()->getDriverName() == 'sqlite';
            $territory = Territory::where('id', $territoryId)->with(
                [
                    'publisher', 'addresses' => function ($query) {
                        $query->orderBy('address', 'asc');
                    }, 'addresses.street', 'addresses.phones.notes' => function ($query) use ($fromDate, $isDBSqlite) {
                        $query->whereRaw(
                            DB::raw("symbol IN (" .
                                Phone::STATUS_NOT_CURRENT_LANGUAGE . "," .
                                Phone::STATUS_NOT_IN_SERVICE . "," .
                                Phone::STATUS_DO_NOT_CALL .
                            ") OR " . 

                            // Note: Use alternative fn for sqlite
                            ($isDBSqlite ? "DATE" : "STR_TO_DATE") .
                            "(date, '%Y-%m-%d') > '" . $fromDate . "'")
                        )
                            ->orderBy('created_at', 'desc');
                    }, 'addresses.notes' => function ($query) use ($fromDate, $isDBSqlite) {
                        $query->whereRaw(
                            DB::raw("archived = '1' OR " .

                            // Note: Use alternative fn for sqlite
                            ($isDBSqlite ? "DATE" : "STR_TO_DATE") .
                            "(date, '%Y-%m-%d') > '" . $fromDate . "'")
                        )
                            ->orderBy('archived', 'desc')
                            ->orderBy('date', 'desc')
                            ->orderBy('created_at', 'desc');
                    }
                ]
            )
                ->get();

            $data = !empty($territory[0]) ? $this->transform($territory[0]->toArray(), 'territory') : null;

        } catch (Exception $e) {
            $data = ['error' => 'Territory not found', 'message' => $e->getMessage()];
        }

        return ['data' => $data];
    }

    public function save(Request $request, $territoryId = null)
    {
        if (!$this->hasAccess($request)) {
            return Response()->json(['error' => 'Access denied.'], 401);
        }

        if (Gate::denies('update-territories')) {
            return Response()
                ->json(['error' => 'Method not allowed'], 403);
        }

        if (!empty($territoryId)) {
            try {
                $territory = Territory::findOrFail($territoryId);
                $currentPublisherId = $territory->publisher_id;
                $data = $territory->update($this->unTransform($request->all(), 'territory'));

                if (array_key_exists('publisherId', $request->all())) {
                    if ($request->input('publisherId') === null || $request->input('publisherId') === 'null') {
                        Record::checkIn($territoryId, $currentPublisherId, $request->input('date'));
                    } else {
                        Record::checkOut($territoryId, $request->input('publisherId'), $request->input('date'));
                    }
                }
            } catch (Exception $e) {
                $data = ['error' => 'Territory not updated', 'message' => $e->getMessage()];
            }
        }

        return ['data' => $data];
    }

    public function add(Request $request)
    {
        if (!$this->hasAccess($request)) {
            return Response()->json(['error' => 'Access denied.'], 401);
        }

        if (Gate::denies('admin')) {
            return Response()
                ->json(['error' => 'Method not allowed'], 403);
        }

        $territoryData = $this->unTransform($request->all(), 'territory');
        $alreadyExist = Territory::where(['number' => $territoryData['number']])->first();
        if (!empty($alreadyExist)) {
            return Response()->json(
                [
                    'error' => 'A territory with Number, "' . $territoryData['number'] . '" already exist.',
                    'data' => ''
                ],
                409 // https://stackoverflow.com/questions/3825990/http-response-code-for-post-when-resource-already-exists
            );
        }

        try {
            // Note: Populate field 'assigned_date' with current date
            $territoryData['assigned_date'] = date('Y-m-d');
            $territory = Territory::create($territoryData);
            $data = !empty($territory) ? $this->transform($territory->toArray(), 'territory') : null;
        } catch (Exception $e) {
            $data = ['error' => 'Territory not updated', 'message' => $e->getMessage()];
        }

        return ['data' => $data];
    }

    public function saveAddress(Request $request, $territoryId = null, $addressId = null)
    {
        if (!$this->hasAccess($request)) {
            return Response()->json(['error' => 'Access denied.'], 401);
        }

        if (empty($territoryId)) {
            return ['error' => 'Territory not found', 'message' => 'Territory not found'];
        }

        if (Gate::denies('update-addresses')) {
            // return Response()->json(['error' => 'Method not allowed'], 403);
            // ***** Return silent error for now, to prevent crash on Mobile apps
            return ['error' => 'Method not allowed', 'data' => null];
        }

        $territory = Territory::find($territoryId);
        if (!Auth::user()->isManager() && empty($territory->publisher_id)) {
            return ['error' => 'Method not allowed', 'message' => 'Territory not assigned', 'data' => null];
        }

        $assignedPublisher = Publisher::find($territory->publisher_id);
        if (!Auth::user()->isManager() && (empty($assignedPublisher) || Auth::user()->id != $assignedPublisher->user_id)) {
            return ['error' => 'Method not allowed', 'message' => 'Territory not assigned to Publisher', 'data' => null];
        }

        if (!empty($addressId)) {
            try {
                $newAddress = $this->unTransform($request->all(), 'address');
                $address = Address::findOrFail($addressId);
                $street = Street::findOrFail($address->street_id)->first();
                try {
                    $data = $address->update($newAddress);
                } catch (\Illuminate\Database\QueryException $queryException) {
                    $error = $queryException->getMessage();
                } catch (Exception $addressUpdateException) {
                    return [
                        'error' => 'There was an error saving this address: ' . $addressUpdateException->getMessage(),
                        'data' => ''
                    ];
                } 
            } catch (Exception $e) {
                $data = ['error' => 'Address not updated', 'message' => $e->getMessage()];
            }
        } else {
            try {
                $transformedData = $this->unTransform($request->all(), 'address');
                if (!empty($request->input('street_street'))) {
                    $transformedData['street'] = [
                        [
                            'street' => $request->input('street_street'),
                            'is_apt_building' => $request->input('street_isAptBuilding')
                        ]
                    ];
                }

                if (!empty($transformedData['street'])) {
                    $street = Street::where('street', $transformedData['street'][0]['street'])->first();
                    if (empty($street)) {
                        $street = Street::create($transformedData['street'][0]);
                    }
                    $transformedData['street_id'] = $street ? $street->id : null;
                }

                $address = Address::where(
                    [
                        'address' => $transformedData['address'],
                        'street_id' => $transformedData['street_id'],
                        'apt' => !empty($transformedData['apt']) ? $transformedData['apt'] : ''
                    ]
                )->first();
                
                // Address already exist?
                if (!empty($address)) {
                    // If in another territory?
                    if ($territoryId != $address->territory_id) {
                        $territoryBelongs = Territory::findOrFail($address->territory_id);
                        return Response()
                            ->json(
                                [
                                    'error' => 'This address belongs to territory ' . $territoryBelongs->number . '. Please contact Admin about moving this address.',
                                    'data' => ''
                                ],
                                202
                            );

                        // If inactive, make it active
                    } else if ($address['inactive']) {
                        $address['inactive'] = 0;
                        $data = $address->update(['inactive', $address['inactive']]);
                    } else {
                        return Response()->json(['error' => 'This address already exists in this territory.', 'data' => ''], 202);
                    }
                } else {
                    $address = !empty($territory) ? $territory->addresses()
                        ->create($transformedData) : null;
                    
                    // Example data:
                    // {"territory_id":29,"name":"Test 2","address":"551","apt":"","phone":"","street_id":732,"updated_at":"2018-03-29 16:50:39","created_at":"2018-03-29 16:50:39","id":6602}
                }

                if (!empty($transformedData['notes'])) {
                    $note = $address->notes()->create($transformedData['notes'][0]);
                    // Example data:
                    // {"date":"2018-03-29","content":"Test Note","entity":"Address","user_id":2,"entity_id":6592,"updated_at":"2018-03-29 16:48:15","created_at":"2018-03-29 16:48:15","id":61182}

                    $note->noteId = $note->id;
                    $address->addressId = $address->id;
                    unset($note->id);
                    $data = (object)array_merge(
                        $address->toArray(), $note->toArray()
                    );
                    // Example data:
                    // {"territory_id":29,"name":"Test 5","address":"551","apt":"","phone":"","street_id":742,"updated_at":"2018-03-29 17:04:52","created_at":"2018-03-29 17:04:52","addressId":6632,"date":"2018-03-29","content":"Test note","entity":"Address","user_id":2,"entity_id":6632,"id":61202,"noteId":61202}

                } else {
                    $data = $address;
                }
            } catch (Exception $e) {
                $data = [
                    'error' => 'Address not added', 'message' => $e->getMessage()
                ];
            }
        }

        return ['data' => $data];
    }

    public function removeAddress(Request $request, $addressId = null)
    {
        if (!$this->hasAccess($request)) {
            return Response()->json(['error' => 'Access denied.'], 401);
        }

        if (Gate::denies('soft-delete-addresses')) {
            return Response()
                ->json(['error' => 'Method not allowed'], 403);
        }

        if (empty($addressId)) {
            return ['error' => 'Address not found', 'message' => 'Address not found'];
        }

        try {
            $address = Address::findOrFail($addressId);

            // delete? (Admin only)
            if ($request->input('delete') && !Gate::denies('delete-addresses')) {
                $data = $address->delete();
            } else {
                // will mark inactive
                $data = $address->update(['inactive' => 1]);
                if ($data && $request->input('note')) {
                    $address->notes()->create(
                        $this->unTransform(
                            [
                                'note' => $request->input('note'), 'date' => date('Y-m-d', time())
                            ],
                            'note'
                        )
                    );
                }
            }
        } catch (Exception $e) {
            $data = ['error' => 'Address not updated', 'message' => $e->getMessage()];
        }
        return ['data' => $data];
    }

    public function addAddressNote(Request $request, $territoryId = null, $addressId = null)
    {
        if (!$this->hasAccess($request)) {
            return Response()->json(['error' => 'Access denied.'], 401);
        }

        if (empty($territoryId)) {
            return ['error' => 'Territory not found', 'message' => 'Territory not found'];
        }

        if (Gate::denies('create-notes')) {
            return Response()->json(['error' => 'Method not allowed'], 403);
        }

        if (!empty($addressId)) {
            $transformedData = $this->unTransform($request->all(), 'note');
            $transformedData['entity'] = 'Address';    
            try {
                $address = Address::findOrFail($addressId);
                $data = !empty($address) && !empty($transformedData) 
                    ? $address->notes()->create($transformedData) 
                    : null;
            } catch (Exception $e) {
                $data = ['error' => 'Note not updated', 'message' => $e->getMessage()];
            }
        } else {
            $data = ['error' => 'Note not saved', 'message' => 'Note not saved'];
        }

        return ['data' => $data];
    }

    public function addPhoneNote(Request $request, $territoryId = null, $phoneId = null)
    {
        if (!$this->hasAccess($request)) {
            return Response()->json(['error' => 'Access denied.'], 401);
        }

        if (empty($territoryId)) {
            return ['error' => 'Territory not found', 'message' => 'Territory not found'];
        }

        if (Gate::denies('create-notes')) {
            return Response()->json(['error' => 'Method not allowed'], 403);
        }

        if (!empty($phoneId)) {
            $transformedData = $this->unTransform($request->all(), 'note');
            $transformedData['entity'] = 'Phone'; 
            try {
                $phone = Phone::findOrFail($phoneId);
                $data = !empty($phone) && !empty($transformedData) 
                    ? $phone->notes()->create($transformedData) 
                    : null;

                $nameChange = $request->input('nameChange');
                if ($nameChange && $nameChange !== $phone->name) {
                    $phone->name = $nameChange;
                    $phone->save();
                }
            } catch (Exception $e) {
                $data = ['error' => 'Note not updated', 'message' => $e->getMessage()];
            }
        } else {
            $data = ['error' => 'Note not saved', 'message' => 'Note not saved'];
        }

        return ['data' => $data];
    }

    public function saveNote(Request $request, $territoryId = null, $noteId = null)
    {
        if (!$this->hasAccess($request)) {
            return Response()->json(['error' => 'Access denied.'], 401);
        }

        if (empty($territoryId)) {
            return ['error' => 'Territory not found', 'message' => 'Territory not found'];
        }

        if (!empty($noteId)) {
            $note = Note::findOrFail($noteId);
            if (Gate::denies('update-notes', $note)) {
                return Response()->json(['error' => 'Method not allowed'], 403);
            }

            try {
                $data = $note->update($this->unTransform($request->all(), 'note'));
            } catch (Exception $e) {
                $data = ['error' => 'Note not updated', 'message' => $e->getMessage()];
            }
        } else {
            $data = ['error' => 'Note not found', 'message' => 'Note not found'];
        }

        return ['data' => $data];
    }

    public function addPhone(Request $request, $territoryId = null, $addressId = null)
    {
        if (!$this->hasAccess($request)) {
            return Response()->json(['error' => 'Access denied.'], 401);
        }

        if (empty($territoryId)) {
            return ['error' => 'Territory not found', 'message' => 'Territory not found'];
        }

        if (!empty($addressId)) {
            if (Gate::denies('create-phones')) {
                return Response()->json(['error' => 'Method not allowed'], 403);
            }

            try {
                $transformedData = $this->unTransform($request->all(), 'phone');
                $address = Address::findOrFail($addressId);
                $data = !empty($address) && !empty($transformedData) 
                    ? $address->phones()->create($transformedData) 
                    : null;
            } catch (Exception $e) {
                $data = ['error' => 'Note not updated', 'message' => $e->getMessage()];
            }
        } else {
            $data = ['error' => 'Note not saved', 'message' => 'Note not saved'];
        }

        return ['data' => $data];
    }

    public function savePhone(Request $request, $territoryId = null, $phoneId = null)
    {
        if (!$this->hasAccess($request)) {
            return Response()->json(['error' => 'Access denied.'], 401);
        }

        if (empty($territoryId)) {
            return ['error' => 'Territory not found', 'message' => 'Territory not found'];
        }

        if (!empty($phoneId)) {
            $phone = Phone::findOrFail($phoneId);
            if (Gate::denies('update-phones', $phone)) {
                return Response()->json(['error' => 'Method not allowed'], 403);
            }

            try {
                $data = $phone->update($this->unTransform($request->all(), 'phone'));
            } catch (Exception $e) {
                $data = ['error' => 'Phone not updated', 'message' => $e->getMessage()];
            }
        } else {
            $data = ['error' => 'Phone not found', 'message' => 'Phone not found'];
        }

        return ['data' => $data];
    }

    public function viewActivities(Request $request, $territoryId = null)
    {
        if (!$this->hasAccess($request)) {
            return Response()->json(['error' => 'Access denied.'], 401);
        }

        try {
            $record = Record::latest()->where(
                'territory_id', $territoryId
            )->with(
                ['user', 'publisher', 'territory']
            )
                ->get();

            $data = !empty($record[0]) ? $this->transformCollection($record, 'record') : null;
        } catch (Exception $e) {
            $data = ['error' => 'Territory activities not found', 'message' => $e->getMessage()];
        }

        return ['data' => $data];
    }

    public function viewAllActivities(Request $request)
    {
        if (!$this->hasAccess($request)) {
            return Response()->json(['error' => 'Access denied.'], 401);
        }

        try {
            $record = Territory::with(['records', 'records.publisher', 'records.user'])->get();
            $data = !empty($record[0]) ? $this->transformCollection($record, 'territory') : null;
        } catch (Exception $e) {
            $data = ['error' => 'Activities not found', 'message' => $e->getMessage()];
        }

        return ['data' => $data];
    }

    public function viewAllNotesActivities(Request $request)
    {
        if (!$this->hasAccess($request)) {
            return Response()->json(['error' => 'Access denied.'], 401);
        }

        try {
            $records = Note::latest()->whereRaw(DB::raw("STR_TO_DATE(date, '%Y-%m-%d') > '" . date('Y-m-d', strtotime("-6 months")) . "'"))
                ->with(['address.territory'])
                ->get();
            $data = !empty($records[0]) ? $this->transform($records, 'territory-notes') : null;
        } catch (Exception $e) {
            $data = ['error' => 'Activities not found', 'message' => $e->getMessage()];
        }

        return ['data' => $data];
    }

    public function map(Request $request, $territoryId = null)
    {
        if (!$this->hasAccess($request)) {
            return Response()->json(['error' => 'Access denied.'], 401);
        }

        if (empty($territoryId)) {
            return ['error' => 'Territory not found', 'message' => 'Territory not found'];
        }

        try {
            $territory = Territory::where('id', $territoryId)->with(
                [
                    'publisher', 'addresses' => function ($query) {
                        $query->where('inactive', '!=', 1)
                            ->orderBy('address', 'asc');
                    },
                    'addresses.street', 'addresses.notes' => function ($query) {
                        $query->orderBy('date', 'desc');
                    }
                ]
            )->get();

            $mapData = !empty($territory[0]) ? Territory::prepareMapData($territory[0]->toArray()) : null;
            $territoryData = !empty($territory[0]) ? $this->transform($territory[0]->toArray(), 'territory') : null;
            if ($territoryData) {
                unset($territoryData['addresses']);
            }

            $data = ['map' => $mapData, 'territory' => $territoryData];
        } catch (Exception $e) {
            $data = ['error' => 'Territory not found', 'message' => $e->getMessage()];
        }

        return ['data' => $data];
    }
}
