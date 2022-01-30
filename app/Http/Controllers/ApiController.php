<?php
namespace App\Http\Controllers;

use Auth;
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
use App\Models\Coordinates;
use Carbon\Carbon;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Mail;

class ApiController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function signup(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (empty($credentials['email']) || empty($credentials['password'])) {
            return Response()->json(
                [
                    'error' => 'User could not be created.',
                    'message' => 'User email and password required.'
                ], 401
            );
        }

        $userExist = User::where(['email' => $credentials['email']])->first();
        if (!empty($userExist)) {
            return Response()->json(
                [
                    'error' => 'User with that email already is in the system',
                    'message' => 'User with that email already is in the system'
                ], 405
            );
        }

        try {
            $credentials['password'] = bcrypt($credentials['password']);
            $credentials['level'] = 1;
            $user = User::create($credentials);
            if (empty($user)) {
                return Response()->json(
                    [
                        'error' => 'User could not be created.',
                        'message' => 'Unknown Error'
                    ], 401
                );
            }

            $this->notifyAdmin(
                $subject = 'User registered', 
                $message = 'New user account signup for  ' . $user->email
            );
        } catch (Exception $e) {
            return Response()->json(
                [
                    'error' => 'User could not be created.', 'message' => $e->getMessage()
                ], 401
            );
        } catch (JWTException $e) {
            return Response()->json(
                [
                    'error' => 'could_not_create_token', 'message' => $e->getMessage()
                ], 400
            );
        } catch (\Swift_TransportException $e) {

        }

        $token = JWTAuth::fromUser($user);
        return Response()->json(compact('token'));
    }

    public function signin(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (empty($credentials['email']) || empty($credentials['password'])) {
            return Response()->json(
                [
                    'error' => 'User could not login.',
                    'message' => 'User email and password required.'
                ], 401
            );
        }

        if (!$token = JWTAuth::attempt($credentials)) {
            return Response()->json(false, 401);
        }

        return Response()
            ->json(compact('token'));
    }

    public function authUser(Request $request)
    {
        if (!$this->hasAccess($request)) {
            return Response()->json(['error' => 'Access denied.'], 401);
        }

        $user = Auth::user();

        return Response()->json(
            [
                'data' => [
                    'email' => $user->email,
                    'userId' => $user->id,
                    'userType' => User::getTypeString($user->level),
                ]
            ]
        );
    }

    public function validateServerURL(Request $request)
    {
        return ['success' => true];
    }

    /*
     * activities() Get all activities for territories and publishers
     * @param $request \Illuminate\Http\Request
    */
    public function activities(Request $request)
    {
        if (!$this->hasAccess($request)) {
            return Response()->json(['error' => 'Access denied.'], 401);

            // May need to "deny", https://laravel.com/docs/6.x/upgrade
            // return $this->deny("You must be an editor to edit this post.");
        }

        if (Gate::denies('update-territories')) {
            return Response()->json(['error' => 'Method not allowed'], 403);
        }

        return ['data' => [
            'publishers' => Publisher::latest()
                ->count(), 'territories' => Territory::latest()
                ->count(), 'records' => 255, // Coming soon
        ]];
    }

    /*
     * sendMessage()
     * @param $request \Illuminate\Http\Request
    */
    public function sendMessage(Request $request, $message = '')
    {
        // Disable notifyAdmin for now
        // $sent = $this->notifyAdmin($subject='Message for Admin', $message='Message for Admin '  . $message);
        $sent = null; 
        return ['data' => $sent];
    }

    /*
     * notifyAdmin() Send email to notify Admin
     * @param $subject string
     * @param $content string
    */
    protected function notifyAdmin($subject, $content)
    {
        return Mail::send(
            'translation-all/emails/notice', compact('content', 'subject'), function ($message) use ($subject) {
                $message->to(config('app.mailToEmail'), config('app.mailToName'));
                $message->from(config('app.mailFromEmail'));
                $message->subject($subject);
            }
        );
    }

    /*
     * hasAccess() Check if JWT token is valid
     * @param $request \Illuminate\Http\Request
    */
    protected function hasAccess($request)
    {
        try {
            $token = $this->parseAuthHeader($request);
            JWTAuth::setToken($token);
            $user = JWTAuth::toUser($token);
        } catch (Exception $e) {
            $error = $e->getMessage();
            Log::debug('hasAccess', ['error' => $error, 'Exception' => true]);
        }

        if (empty($user) || !empty($error)) {
            Log::debug('hasAccess', ['error' => $error, 'ExceptionType' => get_class($e)]);

            return false;
        }

        // Note: Login user for applications User functions
        Auth::login($user); 

        return true;
    }

    /*
     * parseAuthHeader() Great technique from jeroenbourgois -> https://github.com/tymondesigns/jwt-auth/issues/106
     * @param $request \Illuminate\Http\Request
     * @param $headerName string
     * @param $method string
    */
    protected function parseAuthHeader(Request $request, $headerName = 'authorization', $method = 'bearer')
    {
        $header = $request->header($headerName);

        if (is_null($header) && function_exists('getallheaders')) {
            $headers = array_change_key_case(getallheaders(), CASE_LOWER);

            if (array_key_exists($headerName, $headers)) {
                $header = $headers[$headerName];
            }
        }

        if (!starts_with(strtolower($header), $method)) {
            return false;
        }

        return trim(str_ireplace($method, '', $header));
    }

    /*
     * transformCollection() Convert collection to API response data
     * @param $collection Result object
     * @param $type Result object type
    */
    protected function transformCollection($collection, $type)
    {
        $transformedCollection = [];
        foreach ($collection as $i => $entity) {
            if (!is_array($entity)) {
                $entity = $entity->toArray();
            }

            // Handle deleted streets
            if (empty($entity['street']) || !is_array($entity['street'])) {
                // Mark as inactive (for admin to view ONLY)
                $entity['inactive'] = 1;
                $entity['street'] = [
                    "street" => "MISSING STREET"
                ];
            }
            
            $transformedCollection[$i] = $this->transform($entity, $type);
        }

        return $transformedCollection;
    }

    /*
     * unTransformCollection() Convert POST data collection data
     * @param $collection Result object
     * @param $type Result object type
    */
    protected function unTransformCollection($collection, $type)
    {
        $transformedCollection = [];
        if (gettype($collection) == 'string') {
            $collection = json_decode($collection);
        }

        foreach ($collection as $i => $entity) {
            if (gettype($entity) != 'array' && gettype($entity) == 'object') {
                $entity = (array)$entity;
            }
            $transformedCollection[$i] = $this->unTransform($entity, $type);
        }

        return $transformedCollection;
    }

    /*
     * transform() Convert entity to API response data
     * @param $entity Result object
     * @param $type Result object type
    */
    protected function transform($entity, $type)
    {
        $transformedData = [];
        if ($type == 'user') {
            foreach (User::$transformationData as $k => $v) {
                if (!empty($entity[$v]) && $k == 'publisher') {
                    $transformedData[$k] = $this->transform($entity[$v], 'publisher');
                } else {
                    $transformedData[$k] = !empty($entity[$v]) ? $entity[$v] : '';
                }
            }
            $transformedData['userType'] = User::getTypeString($entity['level']);

            return $transformedData;
        }

        if ($type == 'publisher') {
            foreach (Publisher::$transformationData as $k => $v) {
                if (!empty($entity[$v]) && $k == 'territories') {
                    $transformedData[$k] = $this->transformCollection($entity[$v], 'territory');
                } else {
                    $transformedData[$k] = !empty($entity[$v]) ? $entity[$v] : '';
                }
            }

            return $transformedData;
        }

        if ($type == 'territory') {
            foreach (Territory::$transformationData as $k => $v) {
                if (!empty($entity[$v]) && $k == 'addresses') {
                    $transformedData[$k] = $this->transformCollection($entity[$v], 'address');
                    $transformedData[$k] = $this->sortArrayByObjKeys($transformedData[$k], $keys = []);
                } else if (!empty($entity[$v]) && $k == 'records') {
                    $transformedData[$k] = $this->transformCollection($entity[$v], 'record');
                } else if (!empty($entity[$v]) && $k == 'publisher') {
                    $transformedData[$k] = $this->transform($entity[$v], 'publisher');
                } else if (!empty($entity[$v]) && in_array($k, Territory::$intKeys)) {
                    $transformedData[$k] = (int)$entity[$v];
                } else {
                    $transformedData[$k] = !empty($entity[$v]) ? $entity[$v] : '';
                }
            }

            return $transformedData;
        }

        if ($type == 'address') {
            foreach (Address::$transformationData as $k => $v) {
                // if has notes, get notes data
                if (!empty($entity[$v]) && $k == 'notes') {
                    $transformedData[$k] = $this->transformCollection($entity[$v], 'note');
                }
                // if has phones, get phones data
                else if (!empty($entity[$v]) && $k == 'phones') {
                    $transformedData[$k] = $this->transformCollection($entity[$v], 'phone');
                }
                // if has "street" (new Street obj), get street data
                else if (!empty($entity[$v]) && $k == 'street') {
                    $transformedData[$k] = $this->transform($entity[$v], 'street');
                    if ($transformedData[$k]['isAptBuilding'] == 1) {
                        $transformedData['isApt'] = true;
                        $transformedData['streetId'] = !empty($transformedData[$k]['streetId']) ? $transformedData[$k]['streetId'] : '';
                        $transformedData['building'] = $transformedData[$k]['street'];
                        $transformedData['streetName'] = Address::getStreet($transformedData[$k]['street']);
                    } else {
                        $transformedData['streetId'] = !empty($transformedData[$k]['streetId']) ? $transformedData[$k]['streetId'] : '';
                    }
                    $transformedData['streetName'] = $transformedData[$k]['street'];
                }
                // get the property/field for address
                else {
                    $transformedData[$k] = !empty($entity[$v]) ? $entity[$v] : '';
                }
            }
            
            if ($transformedData['street']['isAptBuilding'] == 1 && strpos(strtolower($transformedData['address']), 'ap') === false) {
                $transformedData['address'] = 'Apt ' . $transformedData['address'];
            }

            $transformedData['address'] = strtoupper($transformedData['address']);
            $transformedData['inActive'] = $transformedData['inActive'] ? 1 : 0;

            if (empty((float)$transformedData['lat']) || empty((float)$transformedData['long'])) {
                if ($transformedData['street']['isAptBuilding'] == 1 ) {
                    $transformedData['street']['address_id'] = $transformedData['addressId'];
                    $buildingCoordinates = Coordinates::getBuildingCoordinates($transformedData['street'], '');
                } else {
                    $transformedData['id'] = $transformedData['addressId'];
                    $buildingCoordinates = Coordinates::getAddessCoordinates($transformedData, '');
                }

                $transformedData['lat'] = $buildingCoordinates['lat'];
                $transformedData['long'] = $buildingCoordinates['long'];
            }

            return $transformedData;
        }
        if ($type == 'street') {
            foreach (Street::$transformationData as $k => $v) {
                $transformedData[$k] = !empty($entity[$v]) ? $entity[$v] : '';
            }
            $transformedData['street'] = strtoupper($transformedData['street']);
            return $transformedData;
        }
        if ($type == 'note') {
            foreach (Note::$transformationData as $k => $v) {
                $transformedData[$k] = !empty($entity[$v]) || ($entity[$v] === 0) ? $entity[$v] : '';
            }
            return $transformedData;
        }
        if ($type == 'phone') {
            foreach (Phone::$transformationData as $k => $v) {
                // if has notes, get notes data
                if (!empty($entity[$v]) && $k == 'notes') {
                    $transformedData[$k] = $this->transformCollection($entity[$v], 'note');
                } else {
                    $transformedData[$k] = !empty($entity[$v]) ? $entity[$v] : '';
                }
            }
            return $transformedData;
        }
        if ($type == 'record') {
            foreach (Record::$transformationData as $k => $v) {
                if (!empty($entity[$v]) && $k == 'territory') {
                    $transformedData[$k] = $this->transform($entity[$v], 'territory');
                } else if (!empty($entity[$v]) && $k == 'user') {
                    $transformedData[$k] = $this->transform($entity[$v], 'user');
                } else if (!empty($entity[$v]) && $k == 'publisher') {
                    $transformedData[$k] = $this->transform($entity[$v], 'publisher');
                } else $transformedData[$k] = !empty($entity[$v]) ? $entity[$v] : '';
            }
            return $transformedData;
        }
        if ($type == 'territory-notes') {
            $terrData = [];
            foreach ($entity as $n => $notes) {
                if (empty($notes->address->territory)) {
                    continue;
                }

                $terrNum = $notes->address->territory->number;
                if (empty($terrData[$terrNum])) {
                    $terrData[$terrNum] = [];
                }

                array_push($terrData[$terrNum], (object)[
                    'date' => $notes->date,
                ]);
            }
            ksort($terrData);
            $terrInx = 0;
            foreach ($terrData as $terrNum => $terrNotes) {
                $transformedData[$terrInx] = (object)["territoryNumber" => $terrNum, "territoryNotes" => $this->sortTerrNotesByDate($terrNotes)];
                $terrInx++;
            }
            return $transformedData;
        }
    }

    /*
     * unTransform() Convert POST data to entity data
     * @param $entity Result object
     * @param $type Result object type
    */
    protected function unTransform($data, $type)
    {
        $transformedData = [];

        if ($type == 'publisher') {
            foreach (Publisher::$transformationData as $k => $v) {
                if (!empty($entity[$v]) && $k == 'territories') {
                    $transformedData[$k] = $this->transformCollection($entity[$v], 'territory');
                } else {
                    $transformedData[$k] = !empty($entity[$v]) ? $entity[$v] : '';
                }
            }

            return $transformedData;
        }

        if ($type == 'territory') {
            foreach (Territory::$transformationData as $k => $v) {
                if (!empty($data[$k])) {
                    $transformedData[$v] = $data[$k];
                }
                
                if (!empty($data[$k]) && $v == 'assigned_date') {
                    $transformedData[$v] = Carbon::createFromFormat('Y-m-d', $data[$k])->toDateString();
                }

                if (array_key_exists($k, $data) && $v == 'publisher_id' && ($data[$k] === null || $data[$k] === 'null')) {
                    $transformedData[$v] = null;
                }
            }

            return $transformedData;
        }

        if ($type == 'address') {
            $allowedEmptyVars = ['name', 'phone', 'apt'];
            foreach (Address::$transformationData as $k => $v) {
                // if has notes, get notes data
                if (!empty($data[$k]) && $v == 'notes') {
                    $transformedData[$v] = $this->unTransformCollection($data[$k], 'note');
                }
                // if has "street" (new Street obj), get street data
                else if (!empty($data[$v]) && $v == 'street') {
                    $transformedData[$v] = $this->unTransformCollection($data[$k], 'street');
                }
                // get the property/field for address
                else {
                    if (!empty($data[$k])) {
                        $transformedData[$v] = $data[$k];
                    } else if ((in_array($k, $allowedEmptyVars))) {
                        $transformedData[$v] = '';
                    }
                }

                // Make "address" uppercase
                if (!empty($data[$k]) && $v == 'address') {
                    $transformedData[$v] = strtoupper($data[$k]);
                }

                // Set "inactive" property/field for address to 0
                if (array_key_exists($k, $data) 
                    && $v == 'inactive' 
                    && ($data[$k] === '0' || $data[$k] === null || $data[$k] === false)
                ) {
                    $transformedData[$v] = 0;
                }

                // Set default "lat" property/field for address to 0.0
                if ($v == 'lat' && !array_key_exists($k, $data)) {
                    $transformedData[$v] = 0.0;
                }

                // Set default "long" property/field for address to 0.0
                if ($v == 'long' && !array_key_exists($k, $data)) {
                    $transformedData[$v] = 0.0;
                }
            }

            return $transformedData;
        }

        if ($type == 'note') {
            foreach (Note::$transformationData as $k => $v) {
                if (array_key_exists($k, $data)) {
                    $transformedData[$v] = $data[$k];
                }
            }

            // Add default entity
            if (empty($data['entity'])) {
                $transformedData['entity'] = 'Address';
            }

            // Add retain 
            $transformedData['archived'] = 0;
            if (!empty($data['retain']) && $data['retain'] == 1) {
                $transformedData['archived'] = 1;
            }

            // Add note content
            if (!empty($data['content'])) {
                $transformedData['content'] = $data['content'];
            }

            $transformedData['user_id'] = Auth::user()->id;

            return $transformedData;
        }

        if ($type == 'street') {
            foreach (Street::$transformationData as $k => $v) {
                if (!empty($data[$k])) $transformedData[$v] = $data[$k];
            }
            $transformedData['is_apt_building'] = $data['isAptBuilding'] ? 1 : 0;

            return $transformedData;
        }

        if ($type == 'phone') {
            foreach (Phone::$transformationData as $k => $v) {
                if (array_key_exists($k, $data)) {
                    $transformedData[$v] = $data[$k];
                    if ($k == 'status') {
                        $transformedData[$v] = Phone::getValidatedStatus($data[$k]);
                    }
                }
            }

            return $transformedData;
        }
    }

    /*
     * sortArrayByObjKeys() Convert POST data to entity data
     * @param $data Result array
     * @param $keys Object keys array
    */
    protected function sortArrayByObjKeys($data, $keys = [])
    {
        $sortedData = [];
        $sortedStreets = [];
        foreach ($data as $k => $address) {
            if (empty($sortedStreets[$address['streetId']])) {
                $sortedStreets[$address['streetId']] = [];
            }
            array_push($sortedStreets[$address['streetId']], $address);
        }

        usort(
            $sortedStreets, function ($a, $b) {
                $compared = strcmp($a[0]['street']['street'], $b[0]['street']['street']);
                return $compared;
            }
        );

        foreach ($sortedStreets as $k1 => $street) {
            usort(
                $street, function ($a1, $b1) {
                    $compared2 = intval($a1['address']) > intval($b1['address']);
                    return $compared2;
                }
            );
            $sortedStreets[$k1] = $street;
            foreach ($street as $address2) {
                $sortedData[] = $address2;
            }
        }

        return $sortedData;
    }

    /*
     * sortTerrNotesByDate() Notes data sorted by date
     * @param $data array of Note
    */
    protected function sortTerrNotesByDate($data)
    {
        $dataByDate = [];
        foreach ($data as $k => $noteObj) {
            if (empty($dataByDate[$this->getDateMonth($noteObj->date)])) {
                $dataByDate[$this->getDateMonth($noteObj->date)] = ['notesCount' => 0];
            }

            $dataByDate[
                $this->getDateMonth($noteObj->date)
            ]['notesCount'] = ($dataByDate[
                $this->getDateMonth($noteObj->date)
            ]['notesCount'] + 1);
        }

        return array_values($dataByDate);
    }

    /*
     * getDateMonth() Retrieve month from date
     * @param $date Result string
    */
    protected function getDateMonth($date)
    {
        return date('m', strtotime($date));
    }
}
