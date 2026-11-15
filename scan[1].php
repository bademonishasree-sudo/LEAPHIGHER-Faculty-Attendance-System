<?php
require_once __DIR__ . '/config.php';
require_role('faculty');
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Scan Attendance | LEAPHIGHER</title><link rel="stylesheet" href="assets/style.css"></head>
<body><header class="topbar"><a class="logo" href="dashboard.php">LEAPHIGHER</a><a class="logout" href="dashboard.php">Dashboard</a></header><main class="content scan-content"><section class="panel"><p class="eyebrow">FACULTY CHECK-IN</p><h1>Scan QR Attendance</h1><p class="muted">Allow camera and location access. Scan the active QR code displayed by the administrator.</p><div id="reader"></div><div id="result" class="alert" hidden></div><button class="btn secondary" id="startBtn" type="button">Start scanner</button></section></main>
<script src="https://unpkg.com/html5-qrcode"></script><script>
const resultBox=document.getElementById('result'); let scanner=null; let busy=false;
function show(message,ok){resultBox.hidden=false;resultBox.className='alert '+(ok?'success':'error');resultBox.textContent=message;}
async function submitToken(token){
 if(busy)return; busy=true; show('Checking your location and attendance session…',true);
 if(!navigator.geolocation){show('This browser does not support location access.',false);busy=false;return;}
 navigator.geolocation.getCurrentPosition(async pos=>{
   const body=new URLSearchParams({token,latitude:pos.coords.latitude,longitude:pos.coords.longitude,csrf_token:<?=json_encode(csrf_token())?>});
   try{
     const response=await fetch('submit_attendance.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body});
     const data=await response.json(); show(data.message, data.success);
   }catch(e){show('Could not contact the server. Please try again.',false);}
   busy=false;
 },err=>{show('Location permission is required. Enable GPS/location and try again.',false);busy=false;},{enableHighAccuracy:true,timeout:15000,maximumAge:0});
}
document.getElementById('startBtn').addEventListener('click',()=>{
 if(scanner)return;
 scanner=new Html5Qrcode('reader');
 scanner.start({facingMode:'environment'},{fps:10,qrbox:{width:230,height:230}},decoded=>{
   scanner.pause(true); submitToken(decoded);
 }).catch(err=>{show('Camera could not start. Allow camera permission and use HTTPS or localhost.',false);scanner=null;});
});
</script></body></html>
