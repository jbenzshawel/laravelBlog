<?php

namespace App\Http\Controllers;

use Auth;
use App\Http\Requests;
use App\Repositories\PostsRepository;
use App\Repositories\CommentsRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class PostsController extends BaseController
{
    private $_PostsRepository;

    private $_CommentsRepository;

    private $_UserRepository;

	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        date_default_timezone_set('America/Chicago');
        $this->middleware('auth');
        $this->_PostsRepository = new PostsRepository();
        $this->_CommentsRepository = new CommentsRepository();
        $this->_UserRepository = new UserRepository();
    }

    /**
     * Get posts by id to edit
     *
     * @param int post id
     * @return \Illuminate\Http\Response
     */
    // GET: /posts/{id}/edit
    public function editPost($id)
    {
        $viewData = array();
        if (isset($id)) {
            $viewData["post"] = $this->_PostsRepository->Find($id);
        }

        return view('posts/edit', $viewData);
    }

    /**
     * Show create post page
     *
     * @return \Illuminate\Http\Response
     */
    // GET: /posts/create
    public function create() 
    {
    	$viewData = array(); 
        $viewData["user"] = Auth::user(); 

    	return view('posts/create', $viewData);
    }

    /**
     * Postback for ajax to save a post
     *
     * @param \Illuminate\Http\Request content and userID variables
     * @return string response
     */
    // POST: /posts/create
    public function createPostback(Request $request)
    {
        $status = "false";
        $post = $request->all();
        if (!isset($post["userID"])) $post["userID"] = $request->user()->id;
        if (isset($post["title"]) && isset($post["content"]) && isset($post["userID"])) {
            $this->_PostsRepository->Create([
               "title" => $post["title"], "content" => $post["content"], "UserID" => $post["userID"], "Visible" => true
            ]);
            $status = "true";
        }
        return $status;
    }

    /**
     * Postback for ajax to update a post
     *
     * @param \Illuminate\Http\Request content and userID variables
     * @return string response
     */
    // POST: /posts/update
    public function updatePostback(Request $request)
    {
        $status = "false";
        $post = $request->all();
        if (!isset($post["userID"])) $post["userID"] = $request->user()->id;
        if (isset($post["title"]) && isset($post["content"]) && isset($post["id"])) {
            $this->_PostsRepository->Update([
                "title" => $post["title"], "content" => $post["content"], "Visible" => true, "updated_at" => date("Y-m-d H:i:s")
            ], $post["id"]);
            $status = "true";
        }
        return $status;
    }

    /**
     * Postback for ajax to delete a post
     *
     * @param Request $request postID variable
     * @return string
     */
    // POST: /posts/delete
    public function deletePostback(Request $request)
    {
        if ($this->updatePost($request, 'DeletePost'))
            return "true";
        return "false";
    }

    /**
     * Postback for ajax to hide a post
     *
     * @param Request $request postID variable
     * @return string
     */
    // POST: /posts/hide
    public function hidePostback(Request $request)
    {
        if ($this->updatePost($request, 'HidePost'))
            return "true";
        return "false";
    }

    /**
     * Postback for ajax to show a post
     *
     * @param Request $request postID variable
     * @return string
     */
    // POST: /posts/show
    public function showPostback(Request $request)
    {
        if ($this->updatePost($request, 'ShowPost'))
            return "true";
        return "false";
    }

    public function updatePagination(Request $request)
    {
        $post = $request->all();
        if(isset($post["pagination"])) {
            $this->_UserRepository->Update(['pagination' => $post["pagination"]], $request->user()->id);
            return "true";
        }
        return "false";
    }

    /**
     * Postback for approving a comment
     *
     * @param Request $request
     * @return string true or false of action (laravel requires response to be string)
     */
    // POST: /posts/approveComment
    public function approveCommentPostback(Request $request)
    {
        if ($this->updateComment($request, "ApproveComment"))
            return "true";
        return "false";
    }

    /**
     * Postback for un-approving a comment
     *
     * @param Request $request
     * @return string true or false of action (laravel requires response to be string)
     */
    // POST: /posts/unapproveComment
    public function unapproveCommentPostback(Request $request)
    {
        if ($this->updateComment($request, "UnApproveComment"))
            return "true";
        return "false";
    }

    /**
     * Postback for deleting a comment
     *
     * @param Request $request
     * @return string true or false of action (laravel requires response to be string)
     */
    // POST: /posts/deleteComment
    public function deleteCommentPostback(Request $request)
    {
        if ($this->updateComment($request, "DeleteComment"))
            return "true";
        return "false";
    }

    /**
     * Private function for updating comments.
     *
     * @param Request $request
     * @param $callbackAction is the string name of the static function in the Comments object
     * @return bool response of callback function
     */
    // POST: /posts/updateComment
    private function updateComment(Request $request, $callbackAction)
    {
        $status = false;
        $comment = $request->all();
        if (isset($comment["commentId"]) && gettype($comment["commentId"]) == "integer") {
            $status = true;
            switch ($callbackAction)
            {
                case "ApproveComment":
                    $this->_CommentsRepository->Approve($comment["commentId"]);
                    break;
                case "UnApproveComment":
                    $this->_CommentsRepository->UnApprove($comment["commentId"]);
                    break;
                case "DeleteComment":
                    $this->_CommentsRepository->Delete($comment["commentId"]);
                    break;
                default:
                    $status = false;
                    break;
            }
        }
        return $status;
    }

    /**
     * Private function for updating posts.
     *
     * @param Request $request
     * @param $callbackAction is the string name of the static function in the Posts object
     * @return bool response of callback function
     */
    // POST: /posts/updatePost
    private function updatePost(Request $request, $callbackAction)
    {
        $post = $request->all();
        $status = false;
        if (isset($post["postId"]) && gettype($post["postId"]) == "integer") {
            $status = true;
            switch($callbackAction)
            {
                case "DeletePost":
                    $this->_PostsRepository->Delete($post["postId"]);
                    break;
                case "HidePost":
                    $this->_PostsRepository->HidePost($post["postId"]);
                    break;
                case "ShowPost":
                    $this->_PostsRepository->ShowPost($post["postId"]);
                    break;
                default:
                    $status = false;
                    break;
            }
        }
        return $status;
    }
}