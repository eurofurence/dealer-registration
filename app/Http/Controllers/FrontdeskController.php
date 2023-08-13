<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Enums\ApplicationType;
use App\Http\Requests\CheckInRequest;
use App\Http\Requests\CheckOutRequest;
use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

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
            $user = $application ? $application->user : null;
        }

        $table = $application ? $application->assignedTable : null;

        $parent = $application && $application->type !== ApplicationType::Dealer ? $application->parent()->first() : null;
        $parentApplicant = $parent ? $parent->user : null;

        $children = $application && $application->type === ApplicationType::Dealer ? $application->children : [];
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

    public function comment(CommentRequest $request)
    {
        $this->authorize('create', Comment::class);

        $text = strip_tags($request->get('comment'));
        $application = Application::findOrFail($request->get('application'));

        $author = \Auth::user();
        Comment::create([
            'text' => trim($text),
            'admin_only' => $request->has('admin_only'),
            'user_id' => $author->id,
            'application_id' => $application->id,
        ]);
        return Redirect::route('frontdesk', [
            "search" => $application->user->reg_id,
        ]);
    }

    public function checkIn(CheckInRequest $request)
    {
        $this->authorize('check-in', Application::class);
        $application = Application::findOrFail($request->get('application'));

        abort_if(!$application->checkIn(), 403, 'Application status does not allow check-in.');

        $comment = strip_tags($request->get('ci_comment'));
        $author = \Auth::user();
        Comment::create([
            'text' => trim(join("\n", ['Check-In Performed', $comment])),
            'admin_only' => false,
            'user_id' => $author->id,
            'application_id' => $application->id,
        ]);

        return Redirect::route('frontdesk', [
            "search" => $application->user->reg_id,
        ]);
    }

    public function checkOut(CheckOutRequest $request)
    {
        $this->authorize('check-out', Application::class);
        $application = Application::findOrFail($request->get('application'));

        abort_if(!$application->checkOut(), 403, 'Application status does not allow check-out.');

        $comment = strip_tags($request->get('co_comment'));
        $author = \Auth::user();
        Comment::create([
            'text' => trim(join("\n", ['Check-Out Performed', $comment])),
            'admin_only' => false,
            'user_id' => $author->id,
            'application_id' => $application->id,
        ]);

        return Redirect::route('frontdesk', [
            "search" => $application->user->reg_id,
        ]);
    }
}
