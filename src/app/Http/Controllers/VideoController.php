<?php

namespace App\Http\Controllers;

use App\Models\Youtube;
use Illuminate\Http\Request;
use Google_Client;
use Google_Service_YouTube;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;

class VideoController extends Controller
{
    public function index()
    {
        $youtubes = Youtube::all();
        return view('video/index', compact('youtubes'));
    }

    public function shareMovie()
    {
        return view('video/share');
    }

    public function upShareMovie(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'youtube_url' => ['required', 'regex:/^(http(s)?:\/\/)?((w){3}.)?youtu(be|.be)?(\.com)?\/.+[?=\/]?v[=\/]([a-zA-Z0-9_-]{11})/']
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withInput()
                ->withErrors($validator->errors());
        }

        DB::beginTransaction();

        try {
            $videoId = $this->extractVideoIdFromUrl($request->input('youtube_url'));

            $data = $this->getVideoInfo($videoId);
            // Insert the video data into the "youtubes" table
            DB::table('youtubes')->insert([
                'param_key' => $data['param_key'],
                'title' => $data['title'],
                'description' => $data['description'],
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Commit the transaction if everything goes well
            DB::commit();

            return redirect()->route('home')->with('success', 'Video added successfully!');
        } catch (Exception $e) {
            // Rollback the transaction if something goes wrong
            DB::rollback();

            return redirect()->back()->withErrors(['Error adding video: ' . $e->getMessage()]);
        }
    }

    private function extractVideoIdFromUrl($url)
    {
        $queryString = parse_url($url, PHP_URL_QUERY);
        parse_str($queryString, $queryParameters);
        $videoId = $queryParameters['v'];

        return $videoId;
    }

    private function getVideoInfo($videoId)
    {
        $client = new Google_Client();
        $client->setDeveloperKey(env('YOUTUBE_API_KEY'));
        $youtube = new Google_Service_YouTube($client);

        try {
            $video = $youtube->videos->listVideos('snippet', array('id' => $videoId))->getItems()[0];

            $title = $video['snippet']['title'];
            $description = $video['snippet']['description'];

            return [
                'param_key' => $videoId,
                'title' => $title,
                'description' => $description,
            ];
        } catch (Google_Service_Exception $e) {
            throw new Exception('Google Service Exception: ' . $e);
        }
    }
}
