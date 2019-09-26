<?php

namespace App\Http\Controllers;

use Auth;
use Gate;
use JWTAuth;
use App\User;
use App\Publisher;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;

class PublishersController extends ApiController
{
    public function index(Request $request)
    {
        if (!$this->hasAccess($request)) {
            return Response()->json(['error' => 'Access denied.'], 500);
        }

        if (Gate::denies('view-publishers')) {
            return Response()->json(['error' => 'Method not allowed'], 403);
        }

        return ['data' => $this->transformCollection(Publisher::latest()->with('territories')->get(), 'publisher')];
    }

    public function filter(Request $request)
    {
        if (!$this->hasAccess($request)) {
            return Response()->json(['error' => 'Access denied.'], 500);
        }

        if (Gate::denies('view-publishers')) {
            return Response()->json(['error' => 'Method not allowed'], 403);
        }

        return [
            'data' => $this->transformCollection(
                Publisher::latest()->where(
                    Publisher::applyFilters($request->all())
                )->with('territories')->get(),
                'publisher'
            )
        ];
    }

    public function users(Request $request)
    {
        if (!$this->hasAccess($request)) {
            return Response()->json(['error' => 'Access denied.'], 500);
        }

        if (Gate::denies('admin')) {
            return Response()->json(['error' => 'Method not allowed'], 403);
        }

        return ['data' => $this->transformCollection(User::latest()->with('publisher.territories')->get(), 'user')];
    }

    public function saveUser(Request $request, $userId)
    {
        if (!$this->hasAccess($request)) {
            return Response()->json(['error' => 'Access denied.'], 500);
        }

        if (Gate::denies('admin')) {
            return Response()->json(['error' => 'Method not allowed'], 403);
        }

        try {
            $user = User::findOrFail($userId);
            $user->update(
                [
                    "email" => $request->input('email'),
                    "level" => User::getType($request->input('userType'))
                ]
            );
        } catch (Exception $e) {
            return ['data' => null, 'error' => 'Publisher not found', 'message' => $e->getMessage()];
        }

        return ['data' => $user ? true : null];
    }

    public function attachUser(Request $request)
    {
        if (!$this->hasAccess($request)) {
            return Response()->json(['error' => 'Access denied.'], 500);
        }

        if (Gate::denies('admin')) {
            return Response()->json(['error' => 'Method not allowed'], 403);
        }

        try {
            $publisher = Publisher::findOrFail($request->input('publisherId'));
            $publisher->user_id = $request->input('userId');
            $publisher->save();
        } catch (Exception $e) {
            return ['data' => null, 'error' => 'Publisher not found', 'message' => $e->getMessage()];
        }

        return ['data' => $publisher ? true : null];
    }

    public function deleteUser(Request $request, $userId)
    {
        if (!$this->hasAccess($request)) {
            return Response()->json(['error' => 'Access denied.'], 500);
        }

        if (Gate::denies('admin')) {
            return Response()->json(['error' => 'Method not allowed'], 403);
        }

        try {
            $user = User::findOrFail($userId);
            $data = $user->delete();
        } catch (Exception $e) {
            return ['data' => null, 'error' => 'User not found', 'message' => $e->getMessage()];
        }

        return ['data' => $user ? true : null];
    }

    public function view(Request $request, $publisherId = null)
    {
        if (!$this->hasAccess($request)) {
            return Response()->json(['error' => 'Access denied.'], 500);
        }

        if (Gate::denies('view-publishers')) {
            return Response()->json(['error' => 'Method not allowed'], 403);
        }

        try {
            $publisher = Publisher::where('id', $publisherId)->with('territories')->get();
            $data = !empty($publisher[0]) ? $this->transform($publisher[0]->toArray(), 'publisher') : null;
        } catch (Exception $e) {
            return ['data' => null, 'error' => 'Publisher not found', 'message' => $e->getMessage()];
        }

        return ['data' => $data];
    }

    public function save(Request $request, $publisherId = null)
    {
        if (!$this->hasAccess($request)) {
            return Response()->json(['error' => 'Access denied.'], 500);
        }

        if (Gate::denies('update-publishers')) {
            return Response()->json(['error' => 'Method not allowed'], 403);
        }

        if (!empty($publisherId)) {
            try {
                $publisher = Publisher::findOrFail($publisherId);
                $publisher->update(["first_name" => $request->input('firstName'), "last_name" => $request->input('lastName')]);
                $data = !empty($publisher) ? $this->transform($publisher->toArray(), 'publisher') : null;
            } catch (Exception $e) {
                return ['data' => null, 'error' => 'Publisher not found', 'message' => $e->getMessage()];
            }
        }

        return ['data' => $data];
    }

    public function add(Request $request)
    {
        if (!$this->hasAccess($request)) {
            return Response()->json(['error' => 'Access denied.'], 500);
        }

        if (Gate::denies('update-publishers')) {
            return Response()->json(['error' => 'Method not allowed'], 403);
        }

        try {
            $publisher = Publisher::create(["first_name" => $request->input('firstName'), "last_name" => $request->input('lastName')]);
            $data = !empty($publisher) ? $this->transform($publisher->toArray(), 'publisher') : null;
        } catch (Exception $e) {
            return ['data' => null, 'error' => 'Publisher not created', 'message' => $e->getMessage()];
        }

        return ['data' => $data];
    }

    public function delete(Request $request, $publisherId)
    {
        if (!$this->hasAccess($request)) {
            return Response()->json(['error' => 'Access denied.'], 500);
        }

        if (Gate::denies('admin')) {
            return Response()->json(['error' => 'Method not allowed'], 403);
        }

        try {
            $publisher = Publisher::findOrFail($publisherId);
            $data = $publisher->delete();
        } catch (Exception $e) {
            return ['data' => null, 'error' => 'User not found', 'message' => $e->getMessage()];
        }

        return ['data' => $publisher ? true : null];
    }
}
