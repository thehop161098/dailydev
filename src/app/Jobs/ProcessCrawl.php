<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\DB;
use MongoDB\BSON\UTCDateTime;

class ProcessCrawl implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $nextPage;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($nextPage)
    {
        $this->nextPage = $nextPage;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Log::info('Next Page ' . $this->nextPage . '');
            $client = new Client();
            $headers = [
                'origin' => 'https://app.daily.dev',
                'cookie' => 'ilikecookies=true; das=ZGK8DdDBZrgi2Oa9PzuD6; ory_kratos_session=MTY3OTY1MjE4NXxrSmVLSXNOMV8wTXYtWjZvMmxqUnc2c2JxUF9Od2I1SlJ6NTBMcEFxME5HQTV1dGxxYjh3aXhJa21EQU1nUVJDckhNQUgxRmI2ZE5nbHJiZXNsUmFXNUw3UDlxVGlfV1c2RDExb3VTcmNNbnc3LURkclFkOTNEVFZ4Nm5fOTBVMDcwLVJ5RFlmVlU2S2s4TW1walRuZzhNU2pyNzRzdGxINmxjTWtPTDJXcWQwQWFTa0xGZEdTQ2hZV09LWTFwS3l4S3lUeF81bTlEaVoyR2VIeW5zdEtfRGNRYzZnMkdodTFHWFlYTjJtbGRLVHpiRXp3eWZtakRZaEFBTWl3RmtSXy1pN2cxNnF5amRrfGevI6-e-d_7Ci9ToWkpuEbNucc5iHVpIuV3H2it22aB; da2=7A24n2fcE3sa8STbciqyW; _ga=GA1.2.664211180.1680059181; _gid=GA1.2.904503358.1680059181; da3=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJleHAiOjE2ODAwNzE2OTQuNzQxLCJ1c2VySWQiOiI3QTI0bjJmY0Uzc2E4U1RiY2lxeVciLCJyb2xlcyI6W10sImlhdCI6MTY4MDA3MDc5NCwiYXVkIjoiRGFpbHkiLCJpc3MiOiJEYWlseSBBUEkifQ.7NwkCOHsZlKt0LPioxJhQq7l8-8novAnLcc9GoQMBvw.Oz4ZK7C1IXAO86mQQhf1YgNJ1kr8LFs0I9UGupRif7M',
                'Content-Type' => 'application/json'
            ];
            $body = '{"query":"\\n  query AnonymousFeed(\\n    $loggedIn: Boolean! = false\\n    $first: Int\\n    $after: String\\n    $ranking: Ranking\\n    $version: Int\\n    $supportedTypes: [String!] = [\\"article\\", \\"share\\"]\\n  ) {\\n    page: anonymousFeed(\\n      first: $first\\n      after: $after\\n      ranking: $ranking\\n      version: $version\\n      supportedTypes: $supportedTypes\\n    ) {\\n      ...FeedPostConnection\\n    }\\n  }\\n  \\n  fragment FeedPostConnection on PostConnection {\\n    pageInfo {\\n      hasNextPage\\n      endCursor\\n    }\\n    edges {\\n      node {\\n        ...FeedPost\\n        ...UserPost @include(if: $loggedIn)\\n      }\\n    }\\n  }\\n  \\n  fragment FeedPost on Post {\\n    id\\n    title\\n    createdAt\\n    image\\n    readTime\\n    source {\\n      ...SourceShortInfo\\n    }\\n    sharedPost {\\n      ...SharedPostInfo\\n    }\\n    permalink\\n    numComments\\n    numUpvotes\\n    commentsPermalink\\n    scout {\\n      ...UserShortInfo\\n    }\\n    author {\\n      ...UserShortInfo\\n    }\\n    trending\\n    tags\\n    type\\n    private\\n  }\\n  \\n  fragment SharedPostInfo on Post {\\n    id\\n    title\\n    image\\n    readTime\\n    permalink\\n    commentsPermalink\\n    summary\\n    createdAt\\n    private\\n    scout {\\n      ...UserShortInfo\\n    }\\n    author {\\n      ...UserShortInfo\\n    }\\n    type\\n    tags\\n    source {\\n      ...SourceShortInfo\\n    }\\n  }\\n  \\n  fragment SourceShortInfo on Source {\\n    id\\n    handle\\n    name\\n    permalink\\n    description\\n    image\\n    type\\n    active\\n  }\\n\\n  \\n  fragment UserShortInfo on User {\\n    id\\n    name\\n    image\\n    permalink\\n    username\\n    bio\\n  }\\n\\n\\n\\n  \\n  fragment UserPost on Post {\\n    read\\n    upvoted\\n    commented\\n    bookmarked\\n  }\\n\\n\\n","variables":{"version":12,"ranking":"POPULARITY","first":50,"loggedIn":true,' . $this->nextPage . '}}';

            $request = new Request('POST', 'https://app.daily.dev/api/graphql', $headers, $body);
            $res = $client->sendAsync($request)->wait();
            $posts = json_decode($res->getBody()->getContents(), true);

            $pageInfo = $posts['data']['page']['pageInfo'];
            Log::info('Info data: ' . json_encode($pageInfo) . '');

            $data = $posts['data']['page']['edges'];
            $hasNextPage = $pageInfo['hasNextPage'];
            $endCursor = $pageInfo['endCursor'];

            $dataMysql = [];

            foreach ($data as $edge) {

                $post = $edge['node'];

                $isCheckMysql = DB::table('posts')->where('post_id', $post['id'])->exists();

                if (!$isCheckMysql) {
                    $dataMysql[] = [
                        'post_id' => $post['id'],
                        'title' => $post['title'],
                        'image' => $post['image'],
                        'readTime' => $post['readTime'],
                        'permalink' => $post['permalink'],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }
            }

            if (!empty($dataMysql)) {
                DB::table('posts')->insert($dataMysql);
                Log::info('Inserted ' . count($dataMysql) . ' posts in mysql successfully!');
            }

            if ($hasNextPage && !empty($endCursor)) {
                $this->nextPage = '"after": "' . $endCursor . '"';
                ProcessCrawl::dispatch($this->nextPage)->onQueue('crawl-processing');
            } else {
                Log::info('Stop process');
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
