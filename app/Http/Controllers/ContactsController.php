<?php

namespace App\Http\Controllers;

use App\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContactsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contacts = Contact::all();
        return view('contacts.index', [
            'contacts' => $contacts
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:3',
            'phone' => 'required|integer',
            'photo' =>'required|file|image',
        ]);

        try{
            $name = request('name');
            $phone = request('phone');
            $photo = $request->file('photo')->store('photo', 'public');


            return Contact::create(compact('name', 'phone', 'photo'));
        }

        catch (QueryException $e){
            $errors = ['Something wrong with database'];

            return response()->json([
                'success' => false,
                'errors' => $errors,
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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

        $this->validate($request, [
            'name' => 'required|min:3',
            'phone' => 'required|integer|min:9',
            'photo' =>'file|image',
        ]);

        $name = request('name');
        $phone = request('phone');
        $photo = $request->file('photo');

        try{
            $contact = Contact::find($id);

            if ($photo) {
                Storage::disk('public')->delete($contact->photo);
                $photo = $photo->store('photo', 'public');
            } else {
                $photo = $contact->photo;
            }

            $contact->fill(
                compact('name', 'phone', 'photo')
            );
            $contact->save();

            return response()->json([
                'success' => true,
                'data' => $contact,
            ]);

        } catch (QueryException $e){
            $errors = ['Something wrong with database'];

            return response()->json([
                'success' => false,
                'errors' => $errors,
            ]);
        }

//        Contact::updated($request->$id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function destroy($id)
//    {
//         Contact::destroy($id);
//    }

    public function delete( $id)
    {
         Contact::destroy($id);
    }


}
