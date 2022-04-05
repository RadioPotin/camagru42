// The width and height of the captured photo. We will set the
// width to the value defined here, but the height will be
// calculated based on the aspect ratio of the input stream.

let width = 320;    // We will scale the photo width to this
let height = 0;     // This will be computed based on the input stream

// |streaming| indicates whether or not we're currently streaming
// video from the camera. Obviously, we start at false.

let streaming = false;

// The letious HTML elements we need to configure or control. These
// will be set by the startup() function.

// video feed
let video = document.getElementById('video');

// canvas where all the fun happens
let canvas = document.getElementById('canvas');
// canvas child img element where uploaded or taken pictures are drawn
let photo = document.getElementById('photo');

// take picture button
let takepic = document.getElementById('takepic');
// save picture to gallery button
let savepic = document.getElementById('savepic');

// upload image file button
let upload = document.getElementById('upload');

// hidden form to submit when user wants to save his art
let form = document.getElementById('hiddenform');
// input element in the hidden form to send to the php script that queries the DB
let gallery_entry = document.getElementById('gallery_entry');


// show/hide button savepicture
function show_button()
{
  savepic.className = 'show';
}

function hide_button()
{
  savepic.className = 'hide';
}

// Gain access to video stream
navigator.mediaDevices.getUserMedia({video: true, audio: false})
// success callback
  .then(
    function(stream)
    {
      // tying stream object to video element
      video.srcObject = stream;
      // run video
      video.play();
    }
  )
// failure callback
  .catch(
    function(err)
    {
      console.log("An error occurred: " + err);
    }
  );

video.addEventListener('canplay',
  function(ev)
  {
    if (!streaming)
    {
      height = video.videoHeight / (video.videoWidth/width);

      // Firefox currently has a bug where the height can't be read from
      // the video, so we will make assumptions if this happens.
      if (isNaN(height))
      {
        height = width / (4/3);
      }

      video.setAttribute('width', width);
      video.setAttribute('height', height);
      canvas.setAttribute('width', width);
      canvas.setAttribute('height', height);
      streaming = true;
    }
  }, false);


// Fill the photo with an indication that none has been
// captured.
function clearphoto()
{
  let context = canvas.getContext('2d');
  context.fillStyle = "#AAA";
  context.fillRect(0, 0, canvas.width, canvas.height);

  let data = canvas.toDataURL('image/png');
  photo.setAttribute('src', data);
}

// Capture a photo by fetching the current contents of the video
// and drawing it into a canvas, then converting that to a PNG
// format data URL. By drawing it on an offscreen canvas and then
// drawing that to the screen, we can change its size and/or apply
// other changes before drawing it.

function takepicture() {
  let context = canvas.getContext('2d');
  if (width && height) {
    canvas.width = width;
    canvas.height = height;
    context.drawImage(video, 0, 0, width, height);

    let data = canvas.toDataURL('image/png');
    photo.setAttribute('src', data);
  } else {
    clearphoto();
  }
}
takepic.addEventListener('click',
  function(ev)
  {
    takepicture();
    ev.preventDefault();
    show_button();
  },
  false);

clearphoto();

// This part handles the upload of an image through the file input
function handle_image(e){
  let reader = new FileReader();

  reader.onload = function(event){
    let img = new Image();

    img.onload = function(){
      let ctx = canvas.getContext('2d');

      canvas.width = width;
      canvas.height = height;
      ctx.drawImage(img, 0, 0, width, height);
    }
    img.src = event.target.result;
  }
  reader.readAsDataURL(e.target.files[0]);
  show_button();
}

upload.addEventListener('change', handle_image, false);

// This part handles the saving of the image in the canvas
// to a hidden form that sends the data into a PHP script
// php script extract b64 encoded image and saves it to the relevant DB

function ajaxSendPictureToPhp(entry, token) {
  let params = "entry=" + entry + "&token=" + token + "&submit=submit";

  let httpRequest = new XMLHttpRequest();
  httpRequest.open('POST', 'savetogallery.php', true);
  httpRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  httpRequest.send(params);
  return;
}

function data_to_hidden_form(e){
  let pic = canvas.toDataURL();
  let token = document.getElementsByName('token');

  ajaxSendPictureToPhp(pic, token[0].value);
}

savepic.addEventListener('click', data_to_hidden_form, false);
