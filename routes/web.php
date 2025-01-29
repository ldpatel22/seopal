<?php

use Goutte\Client;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\KeywordsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\OsrReportsController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChatGPTController;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

Route::get('/god/create', function(){
    $check = request()->get('c','pisa');
    $emails = request()->get('e',null);

    if($check !== 'filadendron') exit(403);
    if($emails == null) exit(400);

    $emails = explode(',',$emails);
    foreach($emails as $email) {
        $name = 'Korisnik ' . \Illuminate\Support\Str::random('5');
        $pass = \Illuminate\Support\Str::random('16');
        $user = \App\Models\User::make([
            'email' => $email,
            'name' => $name,
            'password' => Hash::make($pass),
        ]);
        $user->save();

        echo '<h1>' . $name . '</h1>';
        echo \route('auth.login') . '<br />';
        echo 'u: <code>' . $email . '</code><br />';
        echo 'p: <code>' . $pass . '</code><br />';
    }
});

Route::get('/', function(){ return redirect()->route('projects.all'); })->name('home');

// Authentication
Route::get('/login',            [AuthController::class, 'loginPage'])->name('auth.login');
Route::get('/logout',           [AuthController::class, 'logout'])->name('auth.logout');
Route::post('/login',           [AuthController::class, 'ajaxLogin'])->name('auth.login.ajax');
Route::get('/register',           [AuthController::class, 'registerPage'])->name('auth.register');
Route::post('/register',          [AuthController::class, 'ajaxRegister'])->name('auth.register.ajax');

// Subscription
Route::get('/subscription',     [SubscriptionController::class, 'subscriptionPage'])->name('subscription.index');

// User
Route::get('/user/profile',     [UserController::class, 'profilePage'])->name('user.profile');
Route::get('/user',             [UserController::class, 'allUsersPage'])->name('user.all');
Route::get('/user/new',         [UserController::class, 'newUserPage'])->name('user.new');
Route::get('/user/{id}',        [UserController::class, 'singleUserPage'])->name('user.single');
Route::patch('/user/profile',   [UserController::class, 'ajaxEditProfile'])->name('user.ajax.editProfile');
Route::patch('/user/password',  [UserController::class, 'ajaxEditPassword'])->name('user.ajax.editPassword');
Route::patch('/user',           [UserController::class, 'ajaxEditUser'])->name('user.ajax.editUser');
Route::delete('/user',          [UserController::class, 'ajaxDeleteUser'])->name('user.ajax.deleteUser');
Route::post('/user',            [UserController::class, 'ajaxNewUser'])->name('user.ajax.newUser');

// Projects
Route::get('/projects',         [ProjectsController::class, 'allProjectsPage'])->name('projects.all')->middleware(['auth']);
Route::get('/project/{slug}',   [ProjectsController::class, 'singleProjectPage'])->name('projects.single')->middleware(['auth']);
Route::post('/projects',        [ProjectsController::class, 'ajaxNewProject'])->name('projects.ajax.newProject')->middleware(['auth']);
Route::patch('/projects',       [ProjectsController::class, 'ajaxEditProject'])->name('projects.ajax.editProject')->middleware(['auth']);
Route::delete('/projects',      [ProjectsController::class, 'ajaxDeleteProject'])->name('projects.ajax.deleteProject')->middleware(['auth']);
Route::get('/keywords-stats',   [ProjectsController::class, 'ajaxGetKeywordsStats'])->name('projects.ajax.getKeywordsStats')->middleware(['auth','has_focused_project']);

// Projects > External Users
Route::post('/projects/user',   [ProjectsController::class, 'ajaxAddExternalUser'])->name('projects.ajax.newExternalUser')->middleware(['auth']);
Route::delete('/projects/user', [ProjectsController::class, 'ajaxDeleteExternalUser'])->name('projects.ajax.deleteExternalUser')->middleware(['auth']);

// Keywords
Route::get('/keywords',         [KeywordsController::class, 'allKeywordsPage'])->name('keywords.all')->middleware(['auth','has_focused_project']);
Route::get('/keyword/{slug}',   [KeywordsController::class, 'singleKeywordPage'])->name('keywords.single')->middleware(['auth']);
Route::get('/keywords/single',  [KeywordsController::class, 'ajaxGetKeyword'])->name('keywords.ajax.getKeyword')->middleware(['auth','has_focused_project']);
Route::post('/keywords',        [KeywordsController::class, 'ajaxAddKeywords'])->name('keywords.ajax.addKeywords')->middleware(['auth','has_focused_project']);
Route::get('/keywords/stats',   [KeywordsController::class, 'ajaxGetStats'])->name('keywords.ajax.getStats')->middleware(['auth','has_focused_project']);
Route::get('/keywords/related', [KeywordsController::class, 'ajaxGetRelatedKeywords'])->name('keywords.ajax.getRelatedKeywords')->middleware(['auth','has_focused_project']);
Route::delete('/keywords',      [KeywordsController::class, 'ajaxDeleteKeyword'])->name('projects.ajax.deleteKeyword')->middleware(['auth','has_focused_project']);

// Reports
Route::get('/reports',          [ReportsController::class, 'allReportsPage'])->name('reports.all')->middleware(['auth','has_focused_project']);
Route::get('/report/{slug}',    [ReportsController::class, 'singleReportPage'])->name('reports.single')->middleware(['auth']);
Route::get('/print-report/{slug}',[ReportsController::class, 'printReport'])->name('reports.print')->middleware(['auth']);
Route::get('/report-section',   [ReportsController::class, 'ajaxGetReportSection'])->name('reports.ajax.getSection')->middleware(['auth','has_focused_project']);
Route::get('/report-progress',  [ReportsController::class, 'ajaxGetReportProgress'])->name('reports.ajax.getProgress')->middleware(['auth','has_focused_project']);
Route::post('/new-report',      [ReportsController::class, 'ajaxStartNewReport'])->name('reports.ajax.newReport')->middleware(['auth','has_focused_project']);
Route::delete('/reports',       [ReportsController::class, 'ajaxDeleteReport'])->name('reports.ajax.deleteReport')->middleware(['auth','has_focused_project']);
Route::delete('/planner',       [ReportsController::class, 'ajaxDeletePlanner'])->name('reports.ajax.deletePlanner')->middleware(['auth','has_focused_project']);


Route::get('/reports/osr/stats',        [OsrReportsController::class, 'ajaxFetchPlannedPhrasesStats'])->name('reports.osr.ajax.stats')->middleware(['auth','has_focused_project']);
Route::get('/reports/osr/backlinks',    [OsrReportsController::class, 'ajaxFetchBacklinks'])->name('reports.osr.ajax.backlinks')->middleware(['auth','has_focused_project']);
Route::post('/reports/osr/blacklist',   [OsrReportsController::class, 'ajaxBlacklistPhrase'])->name('reports.osr.ajax.blacklist')->middleware(['auth','has_focused_project']);
Route::post('/reports/osr/plan',        [OsrReportsController::class, 'ajaxPlanPhrase'])->name('reports.osr.ajax.plan')->middleware(['auth','has_focused_project']);
Route::post('/reports/osr/backlink',    [OsrReportsController::class, 'ajaxPlanBacklink'])->name('reports.osr.ajax.backlink')->middleware(['auth','has_focused_project']);
Route::post('/reports/osr/bonus',       [OsrReportsController::class, 'ajaxPlanBonus'])->name('reports.osr.ajax.bonus')->middleware(['auth','has_focused_project']);
Route::post('/reports/osr/import',      [OsrReportsController::class, 'ajaxImportPlannedPhrases'])->name('reports.osr.ajax.import')->middleware(['auth','has_focused_project']);

Route::get('/chat', [App\Http\Controllers\ChatGPTController::class, 'askToChatGpt']);

Route::get('/test/semrush/backlinks', function(){
    \App\Services\SemrushApiV3Service::getBacklinksOverviewForUrl('https://webmajstor.ba/seo-optimizacija/');
});
Route::get('/test/urls', function(){
    $data = '[{"domain":"markething.hr","url":"https:\/\/www.markething.hr\/sto-je-seo-optimizacija\/"},{"domain":"wikipedia.org","url":"https:\/\/hr.wikipedia.org\/wiki\/Optimizacija_web_stranice"},{"domain":"grm.digital","url":"https:\/\/grm.digital\/bs\/blog\/technical-seo-key-facts"},{"domain":"marketingstrategije.hr","url":"https:\/\/marketingstrategije.hr\/seo-optimizacija\/"},{"domain":"webmajstor.ba","url":"https:\/\/webmajstor.ba\/seo-optimizacija\/"},{"domain":"kakonapravitiwebstranicu.com","url":"https:\/\/kakonapravitiwebstranicu.com\/seo\/"},{"domain":"arbona.hr","url":"https:\/\/www.arbona.hr\/blog\/seo\/zelite-imati-bolju-poziciju-na-googleu-evo-seo-liste-za-kvalitetnu-optimizaciju\/2825"},{"domain":"webstudio.ba","url":"https:\/\/www.webstudio.ba\/web\/seo-optimizacija\/"},{"domain":"it-akademija.com","url":"https:\/\/www.it-akademija.com\/seo-optimizacija-kompletan-vodic"},{"domain":"tilio.hr","url":"https:\/\/tilio.hr\/seo-optimizacija-vodic-za-pocetnike\/"}]';
    $urls = array_map(function ($s) { return $s->url; }, json_decode($data));
    $repo = new \App\Repositories\UrlBacklinksOverviewStatsRepository();
    $repo->get($urls);
    dd('kraj');
});

Route::get('/test/osr', function(){

    $url = "https://hr.wikipedia.org/wiki/Optimizacija_web_stranice";

    $apiCall = new \App\Models\ApiCall([
        'api' => 'scraper',
        'hash' => 'milos',
        'request' => json_encode(['url' => $url])
    ]); $apiCall->save();

    try {
        $client = new Client(HttpClient::create([
            'verify_peer' => false,
            'verify_host' => false
        ]));
        $crawler = $client->request('GET', $url, [
            'allow_redirects' => true, // make it possible for landing page to redirect
            'verify' => false, // disable SSL certificate check
            'connect_timeout' => config('reports.osr.scraping.timeout'),
            'synchronous' => true,
        ]);
    } catch (\Exception $e) {
        throw new \Exception('Error scraping ' . $url . ' :'  . $e->getMessage());
    }

    $pageHost = parse_url($url)['host'];
    $data = [];

    # region processin

    // html
    {
        $html = $crawler->html();
        $data['html'] = $html;
    }

    // title
    {
        // <title></title>
        $title = null;
        $crawler->filter('title')->each(function ($node) use (&$title) {
            $title = $node->text();
        });
        $data['title'] = $title;
    }

    // description
    {
        // <meta name="description" content="" />
        $description = null;
        $crawler->filter('meta[name="description"],meta[name="Description"]')->each(function ($node) use (&$description) {
            $description = $node->attr('content');
        });
        $data['description'] = $description;
    }

    // preview image
    {
        // <meta property="og:image" content="">
        // <meta name="twitter:image" content="">
        $previewImage = null;
        $crawler->filter('meta[property="og:image"]')->each(function ($node) use (&$previewImage) {
            $previewImage = $node->attr('content');
        });
        if(!$previewImage) $crawler->filter('meta[name="twitter:image"]')->each(function ($node) use (&$previewImage) {
            $previewImage = $node->attr('content');
        });
        $data['previewImage'] = $previewImage;
    }

    // titles
    {
        $titles = [];
        //387: Change to only h1 and h2
        $crawler->filter('h1,h2,h3,h4,h5,h6')->each(function ($node) use (&$titles) {
            $titles[] = [
                'level' => intval(str_replace('h','',$node->nodeName())),
                'text' => $node->text(),
            ];
        });
        $data['titles'] = $titles;
    }

    // images
    {
        // <img src="" alt="" />
        $images = [];
        $crawler->filter('img')->each(function ($node) use (&$images) {
            $image = [];
            $image['src'] = $node->attr('src');
            $image['alt'] = $node->attr('alt');
            $images[] = $image;
        });
        $data['images'] = $images;
    }

    // videos
    {
        // <img src="" alt="" />
        $videos = [];
        $crawler->filter('video')->each(function ($node) use (&$videos) { /** @var Crawler $node */
            if($node->attr('src')) {
                $videos[] = ['src' => $node->attr('src')];
            } else {
                $node->children('source')->each(function($child) use (&$videos){
                    if($child->attr('src')) {
                        $videos[] = ['src' => $child->attr('src')];
                    }
                });
            }
        });
        $data['videos'] = $videos;
    }

    // links
    {
        // <a>label</a>
        $links = [];
        $crawler->filter('a')->each(function ($node) use (&$links, $pageHost) {
            $href = $node->attr('href');
            $info = $href ? parse_url($href) : [];
            $host = isset($info['host']) ? $info['host'] : null;
            $scheme = isset($info['scheme']) ? $info['scheme'] : null;
            $hash = isset($info['fragment']) ? $info['fragment'] : null;

            $link = [];
            $link['href'] = $href;
            $link['host'] = $host;
            $link['scheme'] = $scheme;
            $link['hash'] = $hash;
            $link['title'] = $node->attr('title');
            $link['text'] = $node->text();
            $link['external'] = $host && ($host != $pageHost);
            $links[] = $link;
        });
        $data['links'] = $links;
    }

    // word count
    {
        $wordCount = 0;
        $crawler->filter('body')->each(function ($node) use (&$wordCount) {
            $html = $node->html();
            $search = [
                '@<script[^>]*?>.*?</script>@si',  // Strip out javascript
                '@<style[^>]*?>.*?</style>@siU',    // strip style tags
                '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments including CDATA
            ];
            $content = preg_replace($search, '', $html);
            $wordCount = str_word_count(strip_tags(strtolower($content)));
        });
        $data['wordCount'] = $wordCount;
    }

    # endregion

    $apiCall->response = json_encode($data);
    $apiCall->save();

    $landingPage = \App\Models\OsrLandingPage::find(409);
    $landingPage->html = $data['html'];
    $landingPage->title = $data['title'];
    $landingPage->description = $data['description'];
    $landingPage->word_count = $data['wordCount'];
    foreach(['html','title','description','wordCount'] as $key) unset($data[$key]);
    $landingPage->data = json_encode($data);
    $landingPage->save();

    dd($landingPage->data);
});
//Route::get('/test/stats', function(){
//    $repo = new \App\Repositories\UrlBacklinksOverviewStatsRepository();
//    $stats = $repo->get(['https://www.clickguard.com/what-is-click-fraud/']);
//    dd($stats);
//
//    //\App\Models\KeywordStats::dump('seo optimizacija','ba',['test' => 2]);
////    $stats = \App\Models\KeywordStats::retrieve(['seo'],'ba',1000);
////    dd($stats);
//});
//Route::get('/test/semrush/keywordstats', function(){
//    //$keywords = ['seo','seo optimizacija'];
//    $keywords = 'seo';
//    $locale = 'ba';
//    $data = \App\Services\SemrushApiV3Service::getKeywordsOverviewForLocale($keywords,$locale);
//    dd($data);
//});
//Route::get('/test/semrush/organics', function(){
//    $keyword = 'seo';
//    $locale = 'ba';
//    $data = \App\Services\SemrushApiService::getOrganicSearchResultsReport($keyword,$locale,2);
//    dd($data);
//});
//Route::get('/test', function(){
//
//    $headings = \App\Models\OsrHeading::whereReportId(39)->get();
//    $keywords = \App\Models\Keyword::whereProjectId(11)->get();
//
//    foreach($headings as $heading) {
//        $title = strtolower($heading->name);
//        echo '<h1>' . $heading->name . '</h1>';
//        foreach($keywords as $keyword) {
//            /** @var \App\Models\Keyword $keyword */
//            $index = strpos($title,strtolower($keyword->name));
//            echo '[' . $keyword->name . '] = ' . $index . ' ';
//        }
//    }
//
//});
//Route::get('/test/serpapi', function(){
//
//    $keyword = 'ekcem';
//    $locale = 'rs';
//    $data = \App\Services\SerpApiService::getOrganicSearchResultsReport($keyword,$locale);
//    dd($data);
//
//});
//Route::get('/test/seorankmyaddr', function(){
//
//    $url = "https://www.halobeba.rs/stanja-koja-brinu/atopijski-dermatitis-ekcem/";
//    $data = \App\Services\SeoRankMyAddrService::getSemrushUrlReport($url);
//    dd($data);
//
//});
//Route::get('/test/db', function(){
//
//    $query = DB::table('report_logs')->where('report_id', 29);
//    $query->delete();
//
//});
//Route::get('/test/scrape', function(){
//
////    //-filetype:pdf
////    $rat = \App\Services\SerpApiService::getOrganicSearchResultsReport('ekcem -filetype:pdf','ba');
////    dd($rat);
//
//    $url = "https://ordinacija.vecernji.hr/baza-bolesti/bolest/ekcem/";
//    try {
//        $crawler = Goutte::request('GET', $url);
//        echo 'asok';
//    } catch (\Exception $e) {
//        throw new \Exception('Error scraping ' . $url . ' :'  . $e->getMessage());
//    }
//
//});
