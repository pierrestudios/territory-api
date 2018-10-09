<?php
namespace App\Http\Controllers;

use Auth;
use DB;
use Gate;
use JWTAuth;
use Log;
use App\User;
use App\Publisher;
use App\Territory;
use App\Address;
use App\Street;
use App\Note;
use App\Record;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;

class TerritoriesController extends ApiController {
	public function index(Request $request) {
		if (!$this->hasAccess($request)) {
			return Response()->json(['error' => 'Access denied.'], 500);
		}

		return ['data' => $this->transformCollection(Territory::latest()
			->with(['publisher'])
			->get() , 'territory') ];
	}

	public function filter(Request $request) {
		if (!$this->hasAccess($request)) {
			return Response()->json(['error' => 'Access denied.'], 500);
		}

		return ['data' => $this->transformCollection(Territory::latest()
			->where(Territory::getFilters($request->all()))
			->get() , 'territory') ];
	}

	public function availables(Request $request) {
		if (!$this->hasAccess($request)) {
			return Response()->json(['error' => 'Access denied.'], 500);
		}

		return ['data' => $this->transformCollection(Territory::latest()
			->where('publisher_id', null)
			->get(), 'territory') 
		];
	}

	public function view(Request $request, $territoryId = null) {
		if (!$this->hasAccess($request)) {
			return Response()->json(['error' => 'Access denied.'], 500);
		}

		try {
			$territory = Territory::where('id', $territoryId)->with(['publisher', 'addresses' => function ($query) {
				$query->where('inactive', '!=', 1)
					->orderBy('address', 'asc');
			}
			, 'addresses.street', 'addresses.notes' => function ($query) {
				$query->where(function ($query) {
					// Get ONLY 4 Months
					$fromDate = date('Y-m-d', strtotime("-4 months"));

					// Add alternative query for sqlite
					if (DB::connection() && DB::connection()->getDriverName() == 'mysql') {
						$query->whereRaw(DB::raw("archived is not null or date is null or STR_TO_DATE(date, '%Y-%m-%d') > '" . $fromDate . "'"));
					}
					else if (DB::connection() && DB::connection()->getDriverName() == 'sqlite') {
						$query->whereRaw(DB::raw("archived is not null or date is null or DATE(date, '%Y-%m-%d') > '" . $fromDate . "'"));
					}
				})->orderBy('archived', 'desc')
					->orderBy('date', 'desc')
					->orderBy('created_at', 'desc');
			}
			])
				->get();

			// render map data
			$mapData = !empty($territory[0]) ? Territory::prepareMapData($territory[0]->toArray()) : null;
			$data = !empty($territory[0]) ? $this->transform($territory[0]->toArray() , 'territory') : null;
		}
		catch(Exception $e) {
			$data = ['error' => 'Territory not found', 'message' => $e->getMessage() ];
		}
		return ['data' => $data];
	}

	// Same as view, but with "inactives" (for Admin)
	public function viewAll(Request $request, $territoryId = null) {
		if (!$this->hasAccess($request)) {
			return Response()->json(['error' => 'Access denied.'], 500);
		}

		try {
			// Log Queries
			// DB::enableQueryLog();
			$territory = Territory::where('id', $territoryId)->with(['publisher', 'addresses' => function ($query) {
				$query->orderBy('address', 'asc');
			}
			, 'addresses.street', 'addresses.notes' => function ($query) {
				$query->where(function ($query) {
					// $fromDate = date('Y-m-d', strtotime("-4 months"));
					// $query->whereNull('date')->orWhere(DB::raw("STR_TO_DATE(date) <= '". $fromDate ."'"));
					// $query->whereNull('date')->orWhere('date', '2015-08-08');
					// where needs two parameters, and you have only one. Use whereRaw instead.
					// $query->whereRaw(DB::raw("date is null or date = '2015-08-08'"));
					// $query->whereRaw(DB::raw("archived is not null or date is null or STR_TO_DATE(date, '%Y-%m-%d') > '". $fromDate ."'"));
					
				})->orderBy('date', 'desc')
					->orderBy('archived', 'desc');
			}
			])
				->get();

			$data = !empty($territory[0]) ? $this->transform($territory[0]->toArray() , 'territory') : null;

			// Get Query Log
			// $queries = DB::getQueryLog();
			// $last_query = end($queries);
			// $data['query'] = $last_query;
			
		}
		catch(Exception $e) {
			$data = ['error' => 'Territory not found', 'message' => $e->getMessage() ];
		}
		return ['data' => $data];
	}

	public function save(Request $request, $territoryId = null) {
		if (!$this->hasAccess($request)) {
			return Response()->json(['error' => 'Access denied.'], 500);
		}

		if (Gate::denies('update-territories')) {
			return Response()
				->json(['error' => 'Method not allowed'], 403);
		}

		if (!empty($territoryId)) {
			// dd($this->unTransform($request->all(), 'territory'));
			try {
				$territory = Territory::findOrFail($territoryId);
				$currentPublisherId = $territory->publisher_id;
				$data = $territory->update($this->unTransform($request->all() , 'territory'));

				// Add a Record entry
				if (array_key_exists('publisherId', $request->all())) {
					if ($request->input('publisherId') === null || $request->input('publisherId') === 'null') Record::checkIn($territoryId, $currentPublisherId, $request->input('date'));
					else Record::checkOut($territoryId, $request->input('publisherId') , $request->input('date'));
				}
			}
			catch(Exception $e) {
				$data = ['error' => 'Territory not updated', 'message' => $e->getMessage() ];
			}
		}
		return ['data' => $data];
	}

	public function add(Request $request) {
		if (!$this->hasAccess($request)) {
			return Response()->json(['error' => 'Access denied.'], 500);
		}

		if (Gate::denies('admin')) {
			return Response()
				->json(['error' => 'Method not allowed'], 403);
		}

		// dd($this->unTransform($request->all(), 'territory'));
		try {
			$territory = Territory::create($this->unTransform($request->all() , 'territory'));
			$data = !empty($territory) ? $this->transform($territory->toArray() , 'territory') : null;
		}
		catch(Exception $e) {
			$data = ['error' => 'Territory not updated', 'message' => $e->getMessage() ];
		}
		return ['data' => $data];
	}

	public function saveAddress(Request $request, $territoryId = null, $addressId = null) {
		if (!$this->hasAccess($request)) {
			return Response()->json(['error' => 'Access denied.'], 500);
		}

		if (empty($territoryId)) {
			return ['error' => 'Territory not found', 'message' => 'Territory not found'];
		}

		if (Gate::denies('update-addresses')) {
			// return Response()->json(['error' => 'Method not allowed'], 403);
			// ***** Return silent error for now, to prevent crash
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
				$newAddress = $this->unTransform($request->all() , 'address');
				$address = Address::findOrFail($addressId);
				$street = Street::findOrFail($address->street_id)
					->first();
				// if(!$street->is_apt_building)
				// $newAddress['lat'] = 0;
				// return ['newAddress' => $newAddress, '$address' => $address];
				$error = '';
				try {
					$data = $address->update($newAddress);
				}
				catch(\Illuminate\Database\QueryException $qEx) {
					//return [$qEx];
					$error = $qEx->getMessage();
				}
				catch(Exception $qErr) {
					//return [$qErr];
					$error = $qErr->getMessage();
				}
				// return ['newAddress' => $newAddress, '$address' => $address, '$error'=> $error];
				if ($error) return ['error' => 'There was an error saving this address: ' . $error, 'data' => ''];

			}
			catch(Exception $e) {
				$data = ['error' => 'Address not updated', 'message' => $e->getMessage() ];
			}

		}
		else {
			// dd($request->all());
			// dd($this->unTransform($request->all(), 'address'));
			try {
				// return ['data' => ['street_street' => empty($request->input('street_street')), 'all' => $request->all()]];
				$transformedData = $this->unTransform($request->all() , 'address');
				if (!empty($request->input('street_street'))) {
					$transformedData['street'] = [['street' => $request->input('street_street') , 'is_apt_building' => $request->input('street_isAptBuilding') ]];
				}
				// return ['data' => $transformedData];
				if (!empty($transformedData['street'])) {
					$street = Street::where('street', $transformedData['street'][0]['street'])->first();
					if (empty($street)) $street = Street::create($transformedData['street'][0]);
					// $addressWithStreet = ($address && $street) ? $address->street()->associate($street) : $address;
					$transformedData['street_id'] = $street ? $street->id : null;
				}

				$address = Address::where(['address' => $transformedData['address'], 'street_id' => $transformedData['street_id'], 'apt' => !empty($transformedData['apt']) ? $transformedData['apt'] : ''])->first();
				// Address alredy exist?
				if (!empty($address)) {

					// dd($address);
					// If in another territory?
					if ($territoryId != $address->territory_id) {
						$territoryBelongs = Territory::findOrFail($address->territory_id);
						return Response()
							->json(['error' => 'This address belongs to territory ' . $territoryBelongs->number . '. Please contact Admin about moving this address.', 'data' => ''], 202);
						// Error: "This address belongs to territory number []"
						// If inactive, make it active
						
					}
					else if ($address['inactive']) {
						$address['inactive'] = 0;
						$data = $address->update(['inactive', $address['inactive']]);
					}
					else {
						return Response()->json(['error' => 'This address already exists in this territory.', 'data' => ''], 202);
					}
				}
				else {
					$address = !empty($territory) ? $territory->addresses()
						->create($transformedData) : null;
					// {"territory_id":29,"name":"Test 2","address":"551","apt":"","phone":"","street_id":732,"updated_at":"2018-03-29 16:50:39","created_at":"2018-03-29 16:50:39","id":6602}
					
				}
				if (!empty($transformedData['notes'])) {
					$note = $address->notes()
						->create($transformedData['notes'][0]);
					// {"date":"2018-03-29","content":"Test Note","entity":"Address","user_id":2,"entity_id":6592,"updated_at":"2018-03-29 16:48:15","created_at":"2018-03-29 16:48:15","id":61182}
					$note->noteId = $note->id;
					$address->addressId = $address->id;
					unset($note->id);
					$data = (object)array_merge($address->toArray() , $note->toArray());
					// {"territory_id":29,"name":"Test 5","address":"551","apt":"","phone":"","street_id":742,"updated_at":"2018-03-29 17:04:52","created_at":"2018-03-29 17:04:52","addressId":6632,"date":"2018-03-29","content":"Test note","entity":"Address","user_id":2,"entity_id":6632,"id":61202,"noteId":61202}
					
				}
				else $data = $address;
			}
			catch(Exception $e) {
				$data = ['error' => 'Address not added', 'message' => $e->getMessage() ];
				// {"error":"SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry '200-23' for key 'addresses_address_street_id_unique' (SQL: insert into `addresses` (`name`, `address`, `street_id`, `territory_id`, `updated_at`, `created_at`) values (Jean Marc, 200, 23, 34, 2016-01-14 18:36:49, 2016-01-14 18:36:49))"}
				
			}
		}
		return ['data' => $data];
	}

	public function removeAddress(Request $request, $addressId = null) {
		if (!$this->hasAccess($request)) {
			return Response()->json(['error' => 'Access denied.'], 500);
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
			if ($request->input('delete') && !Gate::denies('delete-addresses')) $data = $address->delete();

			// will mark inactive
			else {
				$data = $address->update(['inactive' => 1]);
				if ($data && $request->input('note')) $address->notes()
					->create($this->unTransform(['note' => $request->input('note') , 'date' => date('Y-m-d', time()) ], 'note'));
			}

		}
		catch(Exception $e) {
			$data = ['error' => 'Address not updated', 'message' => $e->getMessage() ];
		}
		return ['data' => $data];
	}

	public function addNote(Request $request, $territoryId = null, $addressId = null) {
		if (!$this->hasAccess($request)) {
			return Response()->json(['error' => 'Access denied.'], 500);
		}

		if (empty($territoryId)) {
			return ['error' => 'Territory not found', 'message' => 'Territory not found'];
		}

		if (!empty($addressId)) {
			if (Gate::denies('create-notes')) {
				return Response()->json(['error' => 'Method not allowed'], 403);
			}

			// dd($this->unTransform($request->all(), 'note'));
			try {
				$transformedData = $this->unTransform($request->all() , 'note');
				// return ['data' => ['transformed'=>$transformedData, 'request' => $request->all()]];
				$address = Address::findOrFail($addressId);
				// return ['data' => ['address'=>$address]];
				$data = null;
				if (!empty($address) && !empty($transformedData))
					$data = $address->notes()->create($transformedData);
			}
			catch(Exception $e) {
				$data = ['error' => 'Note not updated', 'message' => $e->getMessage() ];
			}
		}
		else {
			$data = ['error' => 'Note not saved', 'message' => 'Note not saved'];
		}
		return ['data' => $data];
	}

	public function saveNote(Request $request, $territoryId = null, $noteId = null) {
		if (!$this->hasAccess($request)) {
			return Response()->json(['error' => 'Access denied.'], 500);
		}

		if (empty($territoryId)) {
			return ['error' => 'Territory not found', 'message' => 'Territory not found'];
		}

		if (!empty($noteId)) {
			$note = Note::findOrFail($noteId);
			if (Gate::denies('update-notes', $note)) {
				return Response()->json(['error' => 'Method not allowed'], 403);
			}

			// Log::info($this->unTransform($request->all(), 'note'));
			try {
				$data = $note->update($this->unTransform($request->all() , 'note'));
			}
			catch(Exception $e) {
				$data = ['error' => 'Note not updated', 'message' => $e->getMessage() ];
			}
		}
		else {
			$data = ['error' => 'Note not found', 'message' => 'Note not found'];
		}
		return ['data' => $data];
	}

	public function viewActivities(Request $request, $territoryId = null) {
		if (!$this->hasAccess($request)) {
			return Response()->json(['error' => 'Access denied.'], 500);
		}

		try {
			$record = Record::latest()->where('territory_id', $territoryId)->with(['user', 'publisher', 'territory'])
				->get();
			dd($record->toArray());
			$data = !empty($record[0]) ? $this->transformCollection($record, 'record') : null;
		}
		catch(Exception $e) {
			$data = ['error' => 'Territory activities not found', 'message' => $e->getMessage() ];
		}
		return ['data' => $data];
	}

	public function viewAllActivities(Request $request) {
		if (!$this->hasAccess($request)) {
			return Response()->json(['error' => 'Access denied.'], 500);
		}

		try {
			$record = Territory::with(['records', 'records.publisher', 'records.user'])->get();
			// dd($this->transformCollection($record, 'territory'));
			$data = !empty($record[0]) ? $this->transformCollection($record, 'territory') : null;
		}
		catch(Exception $e) {
			$data = ['error' => 'Activities not found', 'message' => $e->getMessage() ];
		}
		return ['data' => $data];
	}

	public function viewAllNotesActivities(Request $request) {
		if (!$this->hasAccess($request)) {
			return Response()->json(['error' => 'Access denied.'], 500);
		}

		try {
			$records = Note::latest()->whereRaw(DB::raw("STR_TO_DATE(date, '%Y-%m-%d') > '" . date('Y-m-d', strtotime("-6 months")) . "'"))
				->with(['address.territory'])
				->get();
			$data = !empty($records[0]) ? $this->transform($records, 'territory-notes') : null;
		}
		catch(Exception $e) {
			$data = ['error' => 'Activities not found', 'message' => $e->getMessage() ];
		}
		return ['data' => $data];
	}

	public function map(Request $request, $territoryId = null) {
		if (!$this->hasAccess($request)) {
			return Response()->json(['error' => 'Access denied.'], 500);
		}

		if (empty($territoryId)) {
			return ['error' => 'Territory not found', 'message' => 'Territory not found'];
		}

		try {
			$territory = Territory::where('id', $territoryId)->with(['publisher', 'addresses' => function ($query) {
				$query->where('inactive', '!=', 1)
					->orderBy('address', 'asc');
			}, 
			'addresses.street', 'addresses.notes' => function ($query) {
				$query->orderBy('date', 'desc');
			}
			])
				->get();

			$mapData = !empty($territory[0]) ? Territory::prepareMapData($territory[0]->toArray()) : null;
			$territoryData = !empty($territory[0]) ? $this->transform($territory[0]->toArray() , 'territory') : null;
			if ($territoryData) unset($territoryData['addresses']);

			$data = ['map' => $mapData, 'territory' => $territoryData];
		}
		catch(Exception $e) {
			$data = ['error' => 'Territory not found', 'message' => $e->getMessage() ];
		}
		return ['data' => $data];
	}
}

