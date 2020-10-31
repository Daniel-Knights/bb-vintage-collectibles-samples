<?php

namespace App\Http\Controllers;

use App\Item;
use App\Offer;
use App\WatchedItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ItemsController extends Controller
{
    /**
     * Fetch all items.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = Item::all();

        for ($i=0; $i < sizeof($items); $i++) { 
            $items[$i]->images = unserialize($items[$i]->images);
        }

        return response()->json($items);
    }

    /**
     * Create item.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $item = new Item();

        if ($request->get('name')) {
            $item->name = $request->get('name');
        }
        if ($request->get('price')) {
            $item->price = $request->get('price');
        }
        if ($request->get('description')) {
            $item->description = $request->get('description');
        }
        if ($request->get('category')) {
            $item->category = $request->get('category');
        }
        if ($request->get('images')) {
            $length = sizeof($request->get('images'));

            $files = [];

            // If base64 encoded
            if(strlen(json_encode($request->get('images'))) > 1000) {
                // Loop through and format images before storing
                for ($i = 0; $i < $length; $i++) {
                    $exploded = explode(',', $request->get('images')[$i]);
                    $decoded = base64_decode($exploded[1]);

                    // Distinguish between jpg and png
                    if (Str::contains($exploded[0], 'jpeg')) {
                        $extension = 'jpg';
                    } else {
                        $extension = 'png';
                    }
                    
                    // Assign random filename
                    $fileName = Str::random().'.'.$extension;

                    // Push to files array
                    $files[] = $fileName;
                    
                    // Assign path
                    $path = 'item-images/'.$fileName;

                    Storage::put($path, $decoded);
                }

                $files = serialize($files);
            } else {
                $files = serialize($request->get('images'));
            }

            $item->images = $files;
        }

        $item->save();

        return response($item->jsonSerialize(), Response::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $item = Item::find($id);

        if ($request->get('name')) {
            $item->name = $request->get('name');
        }
        if ($request->get('price')) {
            $item->price = $request->get('price');
        }
        if ($request->get('category')) {
            $item->category = $request->get('category');
        }
        if ($request->get('description')) {
            $item->description = $request->get('description');
        }
        if ($request->get('hide_show')) {
            $item->hide_show = $request->get('hide_show');
        }
        if ($request->get('images')) {
            // Separate jpg images from base64 images
            $jpgImages = [];
            $base64Images = [];

            foreach($request->get('images') as $image) {
                strpos($image, '.jpg') !== false 
                    ? $jpgImages[] = $image 
                    : $base64Images[] = $image;
            }

            $files = [];
            $images = unserialize($item->images);
            
            // Separate removed images from kept images of the original images
            foreach($jpgImages as $image) {
                $images[] = $image;
            }
            
            $images = array_diff($images, $jpgImages);
            $lengthDelete = sizeof($images);

            // Delete previously stored images
            for ($i=0; $i < $lengthDelete; $i++) {
                Storage::delete('item-images/'.array_values($images)[$i]);
            }

            if (sizeof($jpgImages) > 0) {
                foreach ($jpgImages as $image) {
                    $files[] = $image;
                }
            }

            // Handle base64 images
            $length = sizeof($base64Images);
            
            for ($i=0; $i < $length; $i++) {
                $exploded = explode(',', $base64Images[$i]);
                
                $decoded = base64_decode($exploded[1]);

                if (Str::contains($exploded[0], 'jpeg')) {
                    $extension = 'jpg';
                } else {
                    $extension = 'png';
                }
                
                $fileName = Str::random().'.'.$extension;

                $files[] = $fileName;
                
                $path = 'item-images/'.$fileName;

                Storage::put($path, $decoded);
            }

            $item->images = serialize($files);
        }
        
        $item->save();

        return response()->json('Successfully Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = Item::find($id);
        $offer = Offer::where('item_id', $id);
        $watched = WatchedItem::where('item_id', $id);

        $offer->delete();
        $item->delete();
        $watched->delete();

        return response()->json('Successfully Deleted');
    }
}
