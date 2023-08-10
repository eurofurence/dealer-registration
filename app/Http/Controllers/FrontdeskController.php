<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Enums\ApplicationType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FrontdeskController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $search = $request->get('search');

        if (empty($search)) {
            return view('frontdesk', [
                'user' => \Auth::user(),
                'search' => null,
                'application' => null,
                'applicant' => null
            ]);
        }

        // 1. search by user user_id
        // 2. search by user name
        $user = User::where('reg_id', $search)->orWhere('name', 'like', $search)->first();
        $application = $user ? Application::where('user_id', $user->id)->first() : null;

        // 2. search by application table_number
        // 4. search by dealership display_name
        if ($application === null) {
            $application = Application::where('table_number', strtoupper($search))->orWhere('display_name', 'like', $search)->first();
            $user = $application ? $application->user()->first() : null;
        }

        $table = $application && $application->assignedTable ? $application->assignedTable->first() : null;

        $parent = $application && $application->parent() ? $application->parent()->first() : null;
        $parentApplicant = $parent ? $parent->user()->first() : null;

        $children = $application && $application->children() ? $application->children()->get() : [];
        $shares = [];
        $assistants = [];
        foreach ($children as $child) {
            if ($child->type === ApplicationType::Share) {
                array_push($shares, $child);
            } elseif ($child->type === ApplicationType::Assistant) {
                array_push($assistants, $child);
            } else {
                Log::warning('Encountered child of unexpected type.', ['parent' => $parent, 'child' => $child]);
            }
        }

        return view('frontdesk', [
            'user' => \Auth::user(),
            'search' => $search,
            'application' => $application,
            'applicant' => $user,
            'table' => $table,
            'parent' => $parent,
            'parentApplicant' => $parentApplicant,
            'shares' => $shares,
            'assistants' => $assistants,
        ]);
    }
}
