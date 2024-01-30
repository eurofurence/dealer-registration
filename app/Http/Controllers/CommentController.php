<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Comment;

class CommentController extends Controller
{
    /**
     * Export a CSV containing all comments.
     */
    public function exportCommentsAdmin()
    {
        /** @var User $user */
        $user = Auth::user();
        abort_if(!$user->isAdmin(), 403, 'Insufficient permissions');

        $headers = [
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=comments.csv',
            'Expires' => '0',
            'Pragma' => 'public'
        ];

        $comments = Comment::getAllCommentsForExport();

        if (!empty($comments)) {
            # add table headers
            array_unshift($comments, array_keys($comments[0]));
        }

        $callback = function () use ($comments) {
            $handle = fopen('php://output', 'w');
            foreach ($comments as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers)->sendContent();
    }
}
