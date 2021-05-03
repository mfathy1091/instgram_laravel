<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Intervention\Image\Facades\Image;

class ProfilesController extends Controller
{
    public function index(User $user)
    {
        // if user is authenticated
        if (auth()->user()){
            // set to true if the auth user following the passed user's profile
            $follows = auth()->user()->following->contains($user->id);
        }else{
            $follows = false;
        }
        
        // dd($follows);

        return view('profiles.index', compact('user', 'follows'));
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user->profile);

        return view('profiles.edit', compact('user'));
    }

    public function update(User $user)
    {
        $this->authorize('update', $user->profile);

        // (0) validate the request
        $data = request()->validate([
            'title' => 'required',
            'description' => 'required',
            'url' => 'url',
            'image' => '',  // leave it unrequired

        ]);
        


        // (1) if the request has an image (user selects a new image), process it and get its path
        if(request('image')){
            // upload into the driver
            $imagePath = request('image')->store('profile', 'public');

            // cut the image into square
            $image = Image::make(public_path("storage/{$imagePath}"))->fit(1000, 1000);
            $image->save();

            // then save its path in the imageArray
            $imageArray = ['image' => $imagePath];
        }

        
        // (2) save the data - auth ensures that it is grabing the authenticated user, any other user passed in will not work
        auth()->user()->profile->update(array_merge(
            $data,
            
            // (a) if imageArrayarray has an image path, then append it to $data
            // (b) if there is no image, append an empty array
            $imageArray ?? [],
        ));
        

        return redirect("/profile/{$user->id}");
    }

}
