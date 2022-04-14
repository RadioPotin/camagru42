//
// The width and height of the captured photo. We will set the
// width to the value defined here, but the height will be
// calculated based on the aspect ratio of the input stream.
//
let renderWidth = 640;    // We will scale the photo width to this
let renderHeight = 0;     // This will be computed based on the input stream

//
// streaming indicates whether or not we're currently streaming
// video from the camera. Obviously, we start at false.
//
let streaming = false;

// video feed
let video = document.getElementById('video');
//canvas where the feed and montages are made
let canvas_feed = document.getElementById('feed');
let canvas_feed_ctx = canvas_feed.getContext('2d');
// canvas where pictures are saved
let canvas_saved = document.getElementById('canvas_saved');

//
// Gain access to video stream
//
navigator.mediaDevices.getUserMedia({video: true, audio: false})
// success callback
  .then(
    function(stream) {
      // tying stream object to video element
      video.srcObject = stream;
      // run video
      video.play();
    }
  )
// failure callback
  .catch(
    function(err) {
      console.log("An error occurred: " + err);
    }
  );

//
// FUNCTION USED TO RESET CANVAS TO VIDEO FEED STATE
//
function reset () {
  canvas_feed_ctx.drawImage(video, 0, 0, renderWidth, renderHeight);
}

//
// Event listener that constantly draws to canvas from video feed
// This is used as an init draw and refresh draw of the canvas while
// user is doing his thing like a retard
//
video.addEventListener('canplay',
  () => {
    if (!streaming)
    {
      function step() {
        reset();
        requestAnimationFrame(step);
      }

      renderHeight = video.videoHeight / (video.videoWidth/renderWidth);

      // Firefox currently has a bug where the height can't be read from
      // the video, so we will make assumptions if this happens.
      if (isNaN(renderHeight))
      {
        renderHeight = renderWidth / (4/3);
      }

      video.setAttribute('width', renderWidth);
      video.setAttribute('height', renderHeight);
      canvas_saved.setAttribute('width', renderWidth);
      canvas_saved.setAttribute('height', renderHeight);
      canvas_feed.setAttribute('width', renderWidth);
      canvas_feed.setAttribute('height', renderHeight);
      streaming = true;
      requestAnimationFrame(step);
    }
  }, false);

//
// image_selector is used as an index for the selection
// of different overlays available, will be changed by user button input
// defaults to 0 for now
//
let image_selector = 0;

//
// images used as overlays to the canvas feed are stored in an undisplayed ul
// in the document for now
//
let images = document.getElementById('overlays').getElementsByTagName('img');

//
// png used for testing and dev
//
let solaire = images[image_selector];

//
// LAYER CLASS
//
class Layer {

  constructor(img) {
    this.src = img;
    this.height = img.height;
    this.width = img.width;
    this.pos_x = 0;
    this.pos_y = 0;
    this.offset_x = this.width / 2;
    this.offset_y = this.height / 2;
  }

  is_colliding(x, y) {
    return ((x >= this.pos_x && x <= this.pos_x + this.width)
      && (y >= this.pos_y && y <= this.pos_y + this.height));
  }

  drawlayer(ctx, x, y) {
    ctx.drawImage(this.src, x, y, this.width, this.height)
  }

}

//
// EVENT LISTENER FOR DRAG AND DROPPING LAYER ?
//
canvas_feed.addEventListener("mousedown", function(event) {

  canvas_feed.addEventListener("mousemove", onMouseMove, false);
  document.addEventListener("mouseup", onMouseUp, false);

  // this is our friendly png Sunbro Solaire of Astora
  let imageLayer = new Layer (solaire);

  // this is the exact position to which solaire is drawn
  let offsetx = event.offsetX - imageLayer.offset_x;
  let offsety = event.offsetY - imageLayer.offset_y;

  /* DEBUG SHIT
    console.log("CLIENT X: " + event.offsetX);
    console.log("CLIENT Y: " + event.offsetY);
    console.log("OFFSET X: " + offsetx);
    console.log("OFFSET Y: " + offsety);
  */

  //
  // Still need experimenting to
  // understand how to user requestAnimationFrame(); properly
  //
  function step() {
    reset();
    imageLayer.drawlayer(canvas_feed_ctx, offsetx, offsety);
    requestAnimationFrame(step);
  }

  function onMouseMove(event) {
    imageLayer.pos_x = event.offsetX;// - offset.x;
    imageLayer.pos_y = event.offsetY;// - offset.y;
    imageLayer.drawlayer(canvas_feed_ctx, event.offsetX, event.offsetY);
  }

  function onMouseUp(event) {
    canvas_feed.removeEventListener("mousemove", onMouseMove);
    document.removeEventListener("mouseup", onMouseUp);
  }

    requestAnimationFrame(step);
});

//
// show && hide button savepicture
//
function show_button()
{
  savepic.style.display = 'flex';
}

function hide_button()
{
  savepic.style.display = 'none';
}

// save picture to gallery button
let savepic = document.getElementById('savepic');
// upload image file button
let upload = document.getElementById('upload');
// input element in the hidden form to send to the php script that queries the DB
let gallery_entry = document.getElementById('gallery_entry');
// take picture button
let takepic = document.getElementById('takepic');

//
// Capture a photo by fetching the current contents of the video
// and drawing it into a canvas, then converting that to a PNG
// format data URL. By drawing it on an offscreen canvas and then
// drawing that to the screen, we can change its size and/or apply
// other changes before drawing it.
//
function takepicture() {
  let context = canvas_saved.getContext('2d');
  if (renderWidth && renderHeight) {
    canvas_saved.width = renderWidth;
    canvas_saved.height = renderHeight;
    context.drawImage(canvas_feed, 0, 0, renderWidth, renderHeight);
  } else {
    return ;
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

//
// This part handles the upload of an image through the file input
//
function handle_image(e){
  let reader = new FileReader();

  reader.onload = function(event){
    let img = new Image();

    img.onload = function(){
      let ctx = canvas_saved.getContext('2d');

      canvas_saved.width = renderWidth;
      canvas_saved.height = renderHeight;
      ctx.drawImage(img, 0, 0, renderWidth, renderHeight);
    }
    img.src = event.target.result;
  }
  reader.readAsDataURL(e.target.files[0]);
  show_button();
}

upload.addEventListener('change', handle_image, false);

//
// This part handles the saving of the image in the canvas
// to a hidden form that sends the data into a PHP script
// php script extract b64 encoded image and saves it to the relevant DB
//
function sendPictureToPhp(entry, token) {
  //TODO urlencoding
  let params = "entry=" + entry + "&token=" + token + "&submit=submit";

  let httpRequest = new XMLHttpRequest();
  httpRequest.open('POST', 'savetogallery.php', true);
  httpRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  httpRequest.send(params);
  return;
}

function data_to_hidden_form(e){
  let pic = canvas_saved.toDataURL();
  let token = document.getElementsByName('token');

  sendPictureToPhp(pic, token[0].value);
}

savepic.addEventListener('click', data_to_hidden_form, false);
