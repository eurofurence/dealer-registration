<?php

namespace App\Http\Controllers\Applications;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApplicationRequest;
use App\Http\Requests\ApplicationUpdateRequest;
use App\Models\Application;
use App\Models\TableType;

class ApplicationController extends Controller
{
    public function create()
    {
        return view('application.create',[
            'table_types' => TableType::all(['id','name']),
            'application' => new Application(),
        ]);
    }

    public function store(ApplicationRequest $request)
    {
        $request->store();
        return \Redirect::route('dashboard');
    }

    public function edit()
    {
        $application = \Auth::user()->applications;
        abort_if(is_null($application),403,'No Registration');
        return view('application.edit',[
            'table_types' => TableType::all(['id','name']),
            "application" => $application
        ]);
    }
    public function update(ApplicationRequest $request)
    {
        $request->update();
        return \Redirect::route('applications.edit')->with('save-successful');
    }

    public function delete()
    {
        return view('application.delete',[
            "application" => \Auth::user()->applications
        ]);
    }

    public function destroy()
    {
        \Auth::user()->applications->cancel();
        return \Redirect::route('dashboard');
    }
}
