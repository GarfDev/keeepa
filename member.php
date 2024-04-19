<?php
require_once 'autoload.php';
//AMember

header('Access-Control-Allow-Origin: *');

//Proxy
$method = getRequestMethod();
$path = getRequestPath();
$headers = getForwardHeaders();
$account = getHelium10Account($db);

$requestURI = '/'.trim(explode('?', $path)[0], '/');
//Blocking

//Remove _cdn from Path 
$path = str_replace('/_member', '', $path);


$headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.97 Safari/537.36';

if(isset($_SERVER['CONTENT_TYPE'])) {
    $headers[] = 'content-type: '.$_SERVER['CONTENT_TYPE'];
}

$headers[] = 'cookie: '.$account['cookies'];


$blockingScript = <<<SCRIPT
   
     <script>function b64nic(str) {percentEncodedStr = atob(str).split('').map(function(c) {return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);}).join('');return decodeURIComponent(percentEncodedStr);};
window.localStorage.setItem('segmentio.3aca7f78-0a0b-48e1-820b-55cd6f2ebf4d.reclaimEnd', b64nic(''));window.localStorage.setItem('ajs_user_id', b64nic('IjE1NDQ5NzQ1MDQi'));window.localStorage.setItem('ajs_user_traits', b64nic('eyJmaXJzdE5hbWUiOiJBbmdlbGEiLCJsYXN0TmFtZSI6Ik1vcnJpcyIsImVtYWlsIjoiY2lhY29zb3VzdXJmMTk3MUB5YWhvby5jb20iLCJwbGFuIjoiSGVsaXVtMTBfRGlhbW9uZF8yNDkiLCJjcmVhdGVkQXQiOiIxNjQ5NjE2NTE1IiwibGFzdExvZ2luIjoiMTY1MDQxNzA3MCIsInN1YnNjcmlwdGlvbkFnZSI6IjEwIiwibXdzVG9rZW5OQSI6IjAiLCJtd3NUb2tlbkZFIjoiMCIsIm13c1Rva2VuRVUiOiIwIiwibXdzVG9rZW5DTiI6IjAiLCJhZHNUb2tlbiI6IjAiLCJwb3J0YWxzRW5hYmxlZEF0IjoibnVsbCIsImZvbGxvd3VwRW5hYmxlZEF0IjoibnVsbCIsInBwY0VuYWJsZWRBdCI6Im51bGwiLCJwcm9maXRzRW5hYmxlZEF0IjoibnVsbCIsIm1hcmtldFRyYWNrZXJFbmFibGVkQXQiOiJudWxsIiwidXRtX3NvdXJjZSI6InVuZGVmaW5lZCIsInV0bV9jYW1wYWlnbiI6InVuZGVmaW5lZCIsInV0bV9tZWRpdW0iOiJ1bmRlZmluZWQiLCJ1dG1fY29udGVudCI6InVuZGVmaW5lZCIsImNoZWNrbGlzdCI6Ik5vdF9TZWxsaW5nX1BhaWQifQ=='));window.localStorage.setItem('wistia-video-progress-1pwinjqi2s', b64nic('eyJyZXN1bWFibGVLZXkiOiI5ZmM4Mzk4X2ZhNWMyYmE1LWMwNTEtNDk4OC1iNTA3LWZhODQyYjM0ZWFkYy1hOGY0NmRlNzgtMDU0ZjRhODJjZjllLWQxMzQifQ=='));window.localStorage.setItem('segmentio.3aca7f78-0a0b-48e1-820b-55cd6f2ebf4d.inProgress', b64nic('e30='));window.localStorage.setItem('intercom.intercom-state-yzizpoku', b64nic('eyJhcHAiOnsiY29sb3IiOiIjMDAzODczIiwic2Vjb25kYXJ5Q29sb3IiOiIjMDA4MUZGIiwic2VsZlNlcnZlU3VnZ2VzdGlvbnNNYXRjaCI6ZmFsc2UsIm5hbWUiOiJIZWxpdW0gMTAiLCJmZWF0dXJlcyI6eyJhbm9ueW1vdXNJbmJvdW5kTWVzc2FnZXMiOnRydWUsImdvb2dsZUFuYWx5dGljcyI6dHJ1ZSwiaHVic3BvdEluc3RhbGxlZCI6ZmFsc2UsImluYm91bmRNZXNzYWdlcyI6dHJ1ZSwibWFya2V0b0VucmljaG1lbnRJbnN0YWxsZWQiOmZhbHNlLCJvdXRib3VuZE1lc3NhZ2VzIjp0cnVlfSwibGF1bmNoZXJMb2dvVXJsIjpudWxsLCJib3VuZFdlYkV2ZW50cyI6W10sImhlbHBDZW50ZXJTaXRlVXJsIjoiaHR0cHM6Ly9pbnRlcmNvbS5oZWxwL2hlbGl1bS0xMCIsImluYm91bmRDb252ZXJzYXRpb25zRGlzYWJsZWQiOmZhbHNlLCJpc0luc3RhbnRCb290RW5hYmxlZCI6dHJ1ZSwiYWxpZ25tZW50IjoicmlnaHQiLCJob3Jpem9udGFsUGFkZGluZyI6MjAsInZlcnRpY2FsUGFkZGluZyI6MjAsImlzRGV2ZWxvcGVyV29ya3NwYWNlIjpmYWxzZSwiY3VzdG9tR29vZ2xlQW5hbHl0aWNzVHJhY2tlcklkIjpudWxsfSwibGF1bmNoZXIiOnsiaXNMYXVuY2hlckVuYWJsZWQiOnRydWV9LCJsYXVuY2hlckRpc2NvdmVyeU1vZGUiOnsiaGFzRGlzY292ZXJlZExhdW5jaGVyIjp0cnVlfSwidXNlciI6eyJyb2xlIjoidXNlciIsImxvY2FsZSI6ImVuIiwiaGFzQ29udmVyc2F0aW9ucyI6dHJ1ZX0sIm1lc3NhZ2UiOnt9LCJjb252ZXJzYXRpb25zIjp7ImJ5SWQiOnt9fSwib3Blbk9uQm9vdCI6eyJ0eXBlIjpudWxsLCJtZXRhZGF0YSI6e319LCJvcGVyYXRvciI6eyJsYXN0Q29tcG9zZXJFdmVudCI6MH19'));window.localStorage.setItem('ab.storage.messagingSessionStart.4d3ca359-c724-43de-9b97-cb9f1fee4769', b64nic('eyJ2IjoxNjUwNDE3MDcxMTg2fQ=='));window.localStorage.setItem('wistia', b64nic('eyJfX2Rpc3RpbGxlcnkiOiI5ZmM4Mzk4X2Y3YmZjMTY1LTQ0MTktNDY4OC04YTg2LWEyMjNmMTNmZTI2YS00NjI0NGQ4ZDctMDk2YWFlYTIzNGFiLWQwMjMiLCJhY2NvdW50c19sb2FkZWQiOnsid2lzdGlhLXByb2R1Y3Rpb25fMzk4MTM3IjoxNjUwNDE3MDczMjYzfSwibWVkaWFzX2xvYWRlZCI6eyIxcHdpbmpxaTJzIjoxNjUwNDE3MDcyODU5LCJwOTk0Zjg2djd2IjoxNjUwNDE3MDczMTkxLCIxMDZnNTdvbG44IjoxNjUwNDE3MDczMjYzfSwidmlzaXRvcl92ZXJzaW9uIjowfQ=='));window.localStorage.setItem('_uetvid_exp', b64nic('TW9uLCAxNSBNYXkgMjAyMyAwMToxMToxMSBHTVQ='));window.localStorage.setItem('segmentio.3aca7f78-0a0b-48e1-820b-55cd6f2ebf4d.queue', b64nic('W10='));window.localStorage.setItem('ab.storage.serverConfig.4d3ca359-c724-43de-9b97-cb9f1fee4769', b64nic('eyJ2Ijp7InMiOiIyLjYuMCIsImwiOjE2NDYwNzIzNTIsImUiOlsiMkZBIENvZGUgU2VudCIsIkFkZHJlc3MgTW9kYWwgQnV0dG9uIENsaWNrZWQiLCJBZGRyZXNzIE1vZGFsIFZpZXdlZCIsIkFkdmVydGlzaW5nIENhbXBhaWduIExhdW5jaGVkIiwiQWR2ZXJ0aXNpbmcgQ2FtcGFpZ24gUGF1c2VkIiwiQWR2ZXJ0aXNpbmcgU3VnZ2VzdGVkIEJpZCBBcHBsaWVkIiwiQWR2ZXJ0aXNpbmcgU3VnZ2VzdGVkIE5lZ2F0aXZlIEtleXdvcmQgQXBwbGllZCIsIkFkdmVydGlzaW5nIFN1Z2dlc3RlZCBOZXcgS2V5d29yZCBBcHBsaWVkIiwiQWxlcnRzIE1vbml0b3JpbmcgRGlzYWJsZWQiLCJCbGFjayBCb3ggQ29tcGV0aXRvcnMgU2VhcmNoZWQiLCJCbGFjayBCb3ggRmlsdGVycyBMb2FkZWQiLCJCbGFjayBCb3ggRmlsdGVycyBTYXZlZCIsIkJsYWNrIEJveCBOaWNoZSBTZWFyY2hlZCIsIkJsYWNrIEJveCBQcm9kdWN0IFRhcmdldGluZyBTZWFyY2hlZCIsIkJvb3N0ZXIgU3Vic2NyaXB0aW9uIFN0YXJ0ZWQiLCJDYW1wYWlnbiBBdXRvbWF0ZWQiLCJDYW1wYWlnbiBBdXRvbWF0aW9uIFN0b3BwZWQiLCJDYW5jZWxsYXRpb24gU3RlcCBDb21wbGV0ZWQiLCJDZXJlYnJvIEFTSU4gQ29tcGV0aXRvcnMgU2VhcmNoZWQiLCJEYXNoYm9hcmQgS2V5d29yZCBTZWFyY2hlZCIsIkRhdGEgSW1wb3J0IEF0dGVtcHRlZCIsIkV4dGVuc2lvbiBJbnZlbnRvcnkgTGV2ZWxzIExhdW5jaGVkIiwiRXh0ZW5zaW9uIEtleXdvcmRzIExhdW5jaGVkIiwiRXh0ZW5zaW9uIExpc3RpbmcgT3B0aW1pemVyIExhdW5jaGVkIiwiRXh0ZW5zaW9uIFByb2ZpdGFiaWxpdHkgQ2FsY3VsYXRvciBMYXVuY2hlZCIsIkV4dGVuc2lvbiBYcmF5IExhdW5jaGVkIiwiRXh0ZW5zaW9uIFhyYXkgTm8gU2VhcmNoZXMgUGFnZSBWaWV3ZWQiLCJGcmFua2Vuc3RlaW4gS2V5d29yZHMgUHJvY2Vzc2VkIiwiSW5ib3VuZCBTaGlwbWVudCBDcmVhdGVkIiwiSW5ib3VuZCBTaGlwbWVudCBFcnJvciBSZWNlaXZlZCIsIkluZGV4IENoZWNrZXIgU2VhcmNoZWQiLCJJbnZlbnRvcnkgUHJvdGVjdGVkIiwiS2V5d29yZCBGb2xkZXIgQ3JlYXRlZCIsIktleXdvcmQgRm9sZGVyIERlbGV0ZWQiLCJLZXl3b3JkIExpc3QgS2V5d29yZHMgQWRkZWQiLCJLZXl3b3JkIExpc3QgS2V5d29yZHMgRGVsZXRlZCIsIktleXdvcmQgTGlzdCBVcGxvYWRlZCIsIktleXdvcmQgVHJhY2tlciBLZXl3b3JkIEFkZGVkIiwiS2V5d29yZCBUcmFja2VyIEtleXdvcmQgRGVsZXRlZCIsIktleXdvcmQgVHJhY2tlciBQcm9kdWN0IEtleXdvcmRzIFRyYWNrZWQiLCJLZXl3b3JkIFRyYWNrZXIgUmVzdWx0cyBFeHBvcnRlZCIsIkxpbWl0IFVwc2VsbCBNb2RhbCBWaWV3ZWQiLCJMb2NhbCBRdWFudGl0eSBFZGl0ZWQiLCJNV1MgRmxvdyBQcmVzc2VkIEJhY2siLCJNV1MgRmxvdyBTdGVwIENvbXBsZXRlZCIsIk1XUyBGbG93IFN0ZXAgVmlld2VkIiwiTWFya2V0IFRyYWNrZXIgLyBNYXJrZXQgQWxlcnRzIFByZWZlcmVuY2VzIENoYW5nZWQiLCJNYXJrZXQgVHJhY2tlciBBU0lOIEFkZGVkIiwiTWFya2V0IFRyYWNrZXIgQ2hhcnQgVmlld2VkIiwiTWFya2V0IFRyYWNrZXIgRmlsdGVyIFVzZWQiLCJNYXJrZXQgVHJhY2tlciBNYXJrZXQgQ3JlYXRlZCIsIk1hcmtldCBUcmFja2VyIE1hcmtldCBEZWxldGVkIiwiTWFya2V0IFRyYWNrZXIgUHJvZHVjdCBJZ25vcmVkIiwiTWFya2V0IFRyYWNrZXIgUHJvZHVjdCBSZXN0b3JlZCIsIk1hcmtldCBUcmFja2VyIFNvcnQgVXNlZCIsIk1hcmtldCBUcmFja2VyIFN1Z2dlc3RlZCBQcm9kdWN0IFRyYWNrZWQiLCJNaXNwZWxsaW5ncyBDaGVja2VkIiwiTW9udGhseSBTYWxlcyIsIk9mZmVyIENoZWNrb3V0IiwiT25ib2FyZGluZyBCYWNrIEJ1dHRvbiBDbGlja2VkIiwiUHJpY2luZyBFeHBlcmltZW50IFZpZXdlZCIsIlB1cmNoYXNlIE9yZGVyIENyZWF0ZWQiLCJQdXJjaGFzZSBPcmRlciBFeHBvcnRlZCIsIlB1cmNoYXNlIE9yZGVyIFN0YXR1cyBDaGFuZ2VkIiwiUmVhc29uIENhbmNlbGxlZCIsIlJlb3JkZXIgU3VnZ2VzdGlvbiBEZXRhaWwgT3BlbmVkIiwiUmVzdG9jayBTdWdnZXN0aW9ucyBNYW51YWxseSBVcGRhdGVkIiwiU2NyaWJibGVzIExpc3RpbmcgQ3JlYXRlZCIsIlNlbGxlciBBc3Npc3RhbnQgRG93bmxvYWRlZCIsIlNwb25zb3JlZCBCcmFuZHMgLSBydWxlcyBpbXBsZW1lbnRlZCIsIlNwb25zb3JlZCBQcm9kdWN0IC0gcnVsZXMgaW1wbGVtZW50ZWQiLCJTdXBwbGllciBFZGl0ZWQiLCJUZXN0MSBCZWxsIE5vdGlmaWNhdGlvbiBDbGlja2VkIiwiVGVzdDEgQmVsbCBOb3RpZmljYXRpb24gU2VudCIsIlRyZW5kc2VyIFNlYXJjaGVkIiwiVmlld2VkIFByb2R1Y3QiLCJYcmF5IExpbWl0IEhpdCIsIlhyYXkgTGltaXQgUmVzZXQiXSwiYSI6WyJUZXN0ICIsImZpcnN0TmFtZSIsImdwOmlkZW50aWZpZWRJblNlZ21lbnRBdCIsImhlbGl1bVBsYW5JZCIsImlkZW50aWZpZWRJblNlZ21lbnRBdCIsImlwX2FkZHJlc3MiLCJsYXN0TG9naW4iLCJsYXN0TmFtZSIsInVzZXJfYWdlIl0sInAiOltdLCJtIjoyMTYwMCwidiI6IkJIR200V2hXbDl1cWIyU0ZRZC1LZE16RlpjRDBKMWFQS1RGZFRKU1h1bjI0czczS0JrOThDNDhaSTlhUEg3cDJmTlR4SVNPZFJKZlhBbFlRdFdBTjBZVT0iLCJjIjp7ImVuYWJsZWQiOnRydWV9fX0='));window.localStorage.setItem('segmentio.3aca7f78-0a0b-48e1-820b-55cd6f2ebf4d.reclaimStart', b64nic(''));window.localStorage.setItem('ab.storage.deviceId.4d3ca359-c724-43de-9b97-cb9f1fee4769', b64nic('eyJ2Ijp7ImciOiJhYzgyYWY5OC1lYjcwLWM4ODctNjVhYS0yMDQ0YmIwYzYwNmIiLCJjIjoxNjUwNDE3MDcxMTg4LCJsIjoxNjUwNDE3MDcxMTg4fX0='));window.localStorage.setItem('_uetsid_exp', b64nic('VGh1LCAyMSBBcHIgMjAyMiAwMToxMToxMSBHTVQ='));window.localStorage.setItem('wistia-video-progress-p994f86v7v', b64nic('eyJyZXN1bWFibGVLZXkiOiI5ZmM4Mzk4XzZlNGNhMTZkLTEzZDItNDQzNS1iMDU1LTE5OGVhNGMyMjRhZC04NmZiODg0OTUtMTQ4MDJlZjIxYWNhLTVhZDgifQ=='));window.localStorage.setItem('_uetsid', b64nic('OTNkZjFjOTBjMDQ2MTFlY2E2N2Y1NzA3Nzc4MTg3ZTk='));window.localStorage.setItem('ab.storage.triggers.ts.4d3ca359-c724-43de-9b97-cb9f1fee4769', b64nic('eyJ2IjoxNjUwNDE3MDcxODUwfQ=='));window.localStorage.setItem('ajs_group_properties', b64nic('e30='));window.localStorage.setItem('wistia-video-progress-106g57oln8', b64nic('eyJyZXN1bWFibGVLZXkiOiI5ZmM4Mzk4XzFlYzg4ZGFkLTI1N2YtNDM3Zi05MTQyLTRjZDk4ZjZmZTM4OS1kZDVlMTY1ZGItMzEwODBjM2E3ZDNhLTk2OGQifQ=='));window.localStorage.setItem('_grecaptcha', b64nic('MDlBTEc1Wnd5MlJ6cE1zWWstMGNld3NzNWN3bVFTWmVLWlhCSWM1VzdDSGZNVm55REtrTC15LWRHY3RaSTE2bUZsSllzNEdkal96TGVjazQtYUY2LXRKOUFGWGc='));window.localStorage.setItem('debug', b64nic(''));window.localStorage.setItem('ab.storage.triggers.4d3ca359-c724-43de-9b97-cb9f1fee4769', b64nic('eyJ2IjpbXX0='));window.localStorage.setItem('loglevel', b64nic('V0FSTg=='));window.localStorage.setItem('ab.storage.device.4d3ca359-c724-43de-9b97-cb9f1fee4769', b64nic('eyJ2Ijp7ImJyb3dzZXIiOiJDaHJvbWUiLCJicm93c2VyX3ZlcnNpb24iOiI5My4wLjQ1NzcuMCIsIm9zX3ZlcnNpb24iOiJXaW5kb3dzIiwicmVzb2x1dGlvbiI6IjgyMHg2MTUiLCJsb2NhbGUiOiJlbi11cyIsInRpbWVfem9uZSI6IkFtZXJpY2EvTG9zX0FuZ2VsZXMiLCJ1c2VyX2FnZW50IjoiTW96aWxsYS81LjAgKFdpbmRvd3MgTlQgNi4zOyBXaW42NDsgeDY0KSBBcHBsZVdlYktpdC81MzcuMzYgKEtIVE1MLCBsaWtlIEdlY2tvKSBDaHJvbWUvOTMuMC40NTc3LjAgU2FmYXJpLzUzNy4zNiJ9fQ=='));window.localStorage.setItem('amplitude_unsent_identify_95d3abbefaf19863dc230d5449736018', b64nic('W10='));window.localStorage.setItem('ab.storage.ccLastFullSync.4d3ca359-c724-43de-9b97-cb9f1fee4769', b64nic('eyJ2IjowfQ=='));window.localStorage.setItem('ajs_group_id', b64nic(''));window.localStorage.setItem('ab.storage.sessionId.4d3ca359-c724-43de-9b97-cb9f1fee4769', b64nic('eyJ2Ijp7ImciOiJlNDFmODRkOC05MTc4LWY5ZWQtNjRlNy1lNjIzMDQ3ZWYxZTMiLCJlIjoxNjUwNDE4ODcxMjE2LCJjIjoxNjUwNDE3MDcxMTg2LCJsIjoxNjUwNDE3MDcxMjE2fX0='));window.localStorage.setItem('amplitude_unsent_95d3abbefaf19863dc230d5449736018', b64nic('W10='));window.localStorage.setItem('_uetvid', b64nic('OTNkZjY1YTBjMDQ2MTFlYzk5Zjg3YjI0NjY3ZWM2ZDM='));window.localStorage.setItem('segmentio.3aca7f78-0a0b-48e1-820b-55cd6f2ebf4d.ack', b64nic('MTY1MDQxNzA4Mjc4OA=='));window.localStorage.setItem('ab.storage.ccLastCardUpdated.4d3ca359-c724-43de-9b97-cb9f1fee4769', b64nic('eyJ2IjowfQ=='));window.localStorage.setItem('undefined', b64nic(''));window.localStorage.setItem('ajs_anonymous_id', b64nic('IjRhMDA3ZTA1LTJiNGEtNGQ2Mi05ZDdkLTllY2RlNGI5N2NkNSI='));window.localStorage.setItem('ab.storage.userId.4d3ca359-c724-43de-9b97-cb9f1fee4769', b64nic('eyJ2Ijp7ImciOiIxNTQ0OTc0NTA0IiwiYyI6MTY1MDQxNzA3MTE4NCwibCI6MTY1MDQxNzA3MTE4NH19'));window.localStorage.setItem('ab.storage.cc.4d3ca359-c724-43de-9b97-cb9f1fee4769', b64nic('eyJ2IjpbXX0='));</script><style>.sweet-overlay{display: none !important;} .sweet-alert{display: none !important;}body{height: auto !important;overflow-y: scroll !important;}</style>

SCRIPT;




if($account) {


    $curl = new \Curl\Curl('https://members.helium10.com'.$path);

    if($method == 'GET') {

        $curl->setOpts([
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_HTTPHEADER => $headers,
        ]);
        $curl->setCookieString($cookies);
        $curl->exec();

        $responseHeaders = $curl->getResponseHeaders();

        if(isset($responseHeaders['location'])) {
            header('Location: '.str_replace(['https://members.helium10.com'], [$cdnDomain], $responseHeaders['location']));
            die();
        }

        http_response_code($curl->getHttpStatusCode());

        $responseHeaders->offsetUnset('status-line');
        $responseHeaders->offsetUnset('date');
        $responseHeaders->offsetUnset('set-cookie');
        $responseHeaders->offsetUnset('location');
        $responseHeaders->offsetUnset('server');
        $responseHeaders->offsetUnset('expires');
        $responseHeaders->offsetUnset('cache-control');
        $responseHeaders->offsetUnset('pragma');
        $responseHeaders->offsetUnset('vary');
        $responseHeaders->offsetUnset('etag');
        $responseHeaders->offsetUnset('last-modified');
        $responseHeaders->offsetUnset('accept-ranges');
        $responseHeaders->offsetUnset('content-length');

        foreach($responseHeaders as $name => $value) {
            header($name.': '.$value);
        }



        $response = $curl->getRawResponse(); //Do blocking here

        $response = str_replace([
            'members.helium10.com',
            'cdn.helium10.com',
            'helium10.com',
            'assets.helium10.com',
            're-cdn.helium10.com',
            'h10-re-frontend.s3.amazonaws.com',
            
            //Variable replace: controller
            '"+controller+"',
            'clarity',
            'litix.io'
        ], [
            
            $memberDomain,
            $cdnDomain,
            $mainDomain,
            $assetsDomain,
            $recdnDomain,
            $amazonDomain,
            //Variable replace: controller
            'amazon',
            'clarity05',
            'l0523itix.io'
        ], $response);

        //Remove GTag
        $response = str_replace([
            'googletagmanager.com/ns.html',
            'googletagmanager.com/gtm.js'
        ], [
            'googletagmanager.com/ns2.html',
            'googletagmanager.com/gtm2.js'
        ], $response);

        //Replace WS Prototype
        $response = str_replace([
            'this.hostname = opts.hostname ||'
        ], [
            'this.hostname = \'members.helium10.com\' ||'
        ], $response);

        die($response);

    } else if($method == 'POST') {

        /*
        if(@preg_match('/application\/x-www-form-urlencoded/', $_SERVER['CONTENT_TYPE'])) {
            $post = http_build_query($_POST);
        } else {
            $post = file_get_contents('php://input');
        }
        */
        $post = file_get_contents('php://input');
        $headers[] = 'content-length: '.strlen($post);

        $curl->setOpts([
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $post,
        ]);
        $curl->setCookieString($cookies);
        $curl->exec();

        $responseHeaders = $curl->getResponseHeaders();

        if(isset($responseHeaders['location'])) {
            header('Location: '.str_replace(['https://members.helium10.com'], [$cdnDomain], $responseHeaders['location']));
            die();
        }

        http_response_code($curl->getHttpStatusCode());

        $responseHeaders->offsetUnset('status-line');
        $responseHeaders->offsetUnset('date');
        $responseHeaders->offsetUnset('set-cookie');
        $responseHeaders->offsetUnset('location');
        $responseHeaders->offsetUnset('server');
        $responseHeaders->offsetUnset('expires');
        $responseHeaders->offsetUnset('cache-control');
        $responseHeaders->offsetUnset('pragma');
        $responseHeaders->offsetUnset('vary');
        $responseHeaders->offsetUnset('etag');
        $responseHeaders->offsetUnset('last-modified');
        $responseHeaders->offsetUnset('accept-ranges');
        $responseHeaders->offsetUnset('content-length');

        foreach($responseHeaders as $name => $value) {
            header($name.': '.$value);
        }

        foreach($curl->getResponseCookies() as $c_name => $c_value) {
            setcookie($c_name, $c_value, 0, '/');
        }

        $response = $curl->getRawResponse(); //Do blocking here

         $response = str_replace([
            'members.helium10.com',
            'helium10.com',
            'cdn.helium10.com',
            'assets.helium10.com',
            're-cdn.helium10.com',
            'h10-re-frontend.s3.amazonaws.com',
            
            //Variable replace: controller
            '"+controller+"',
            'clarity',
            'litix.io'
        ], [
            
            $memberDomain,
            $mainDomain,
            $cdnDomain,
            $assetsDomain,
            $recdnDomain,
            $amazonDomain,
            //Variable replace: controller
            'amazon',
            'clarity05',
            'l0523itix.io'
        ], $response);
        
        

        //Remove GTag
        $response = str_replace([
            'googletagmanager.com/ns.html',
            'googletagmanager.com/gtm.js'
        ], [
            'googletagmanager.com/ns2.html',
            'googletagmanager.com/gtm2.js'
        ], $response);

        //Replace WS Prototype
        $response = str_replace([
            'this.hostname = opts.hostname ||'
        ], [
            'this.hostname = \'members.helium10.com\' ||'
        ], $response);

        die($response);

    } else {

        echo '<script>alert(\'There was an issue while making the request!\');</script>';

    }

} else {

    echo '<html><head><title>Updating Accounts, PLease Wait...</title></head><body><h1>Updating Accounts, Please wait...</h1><script>setTimeout(_ => window.location.href = \'/\', 5000)</script></body></html>';

}