<?php

namespace App\Http\Controllers\Api\Clerk;

use App\User;
use App\Outlet;
use App\OutletUserNotes;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OutletUserNotesController extends Controller
{
    public function store(Request $request, Outlet $outlet, User $user)
    {
    	$outlet->userNote()->create([
    		'user_id'	=> $user->id,
    		'notes'	=> $request->notes,
    	]);
    }

    public function update(Request $request, Outlet $outlet, User $user)
    {
    	$outlet->userNote()->update([
    		'user_id'	=> $user->id,
    		'notes'	=> $request->notes,
    	]);

		return response()->json([
			'status'	=> 1,
			'message'	=> 'User note has been successfully updated.',
		]);
    }

    public function destroy(Outlet $outlet, User $user)
    {
    	if( $outlet->userNote()->delete() )
    	{
			return response()->json([
				'status'	=> 1,
				'message'	=> 'User note has been successfully deleted.',
			]);
    	}

		return response()->json([
			'status'	=> 0,
			'message'	=> "Oops. There's some problem when deleting a note. Please try again.",
		]);    	
    }
}
