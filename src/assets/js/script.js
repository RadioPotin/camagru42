//
// The width and height of the captured photo. We will set the
// width to the value defined here, but the height will be
// calculated based on the aspect ratio of the input stream.
//
let renderWidth = 640;    // We will scale the photo width to this
let renderHeight = 800;     // This will be computed based on the input stream

//
// streaming indicates whether or not we're currently streaming
// video from the camera. Obviously, we start at false.
//
let streaming = false;

// video feed
let video = document.getElementById('video');
// canvas where the feed and montages are made
let canvas_feed = document.getElementById('feed');
let canvas_feed_ctx = canvas_feed.getContext('2d');
// canvas where pictures are saved
let canvas_saved = document.getElementById('canvas_saved');

let source = video;

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
function reset(flux) {
  canvas_feed_ctx.drawImage(flux, 0, 0, renderWidth, renderHeight);
}

//
// Event listener that constantly draws to canvas from video feed
// This is used as an init draw and refresh draw of the canvas while
// user is doing his thing like a retard
//
video.addEventListener('canplay',
  function(event){
    streaming = true;
    renderHeight = video.videoHeight / (video.videoWidth/renderWidth);
    video.setAttribute('width', renderWidth);
    video.setAttribute('height', renderHeight);
    canvas_saved.setAttribute('width', renderWidth);
    canvas_saved.setAttribute('height', renderHeight);
    canvas_feed.setAttribute('width', renderWidth);
    canvas_feed.setAttribute('height', renderHeight);
  }, false);


//
// images used as overlays to the canvas feed are stored in an undisplayed ul
// in the document for now
//
let images =
  [document.getElementById('layer0'),
    document.getElementById('layer1'),
    document.getElementById('layer2'),
    document.getElementById('layer3')];

//
// LAYER CLASS
//
class Layer {

  constructor(i) {
    let img = images[i];
    this.src = img;
    if (img.naturalHeight > renderHeight && img.naturalWidth > renderWidth) {
      this.height = renderHeight / 2;
      this.width = renderWidth / 2;
    } else {
      this.height = img.naturalHeight;
      this.width = img.naturalWidth;
    }
    this.pos_x = 0;
    this.pos_y = 0;
    this.offset_x = this.width / 2;
    this.offset_y = this.height / 2;
  }

  is_colliding(x, y) {
    return ((x >= this.pos_x && x <= this.pos_x + this.width)
      && (y >= this.pos_y && y <= this.pos_y + this.height));
  }

  drawlayer(ctx) {
    ctx.drawImage(this.src, this.pos_x, this.pos_y, this.width, this.height)
  }

  center_on(x, y) {
    this.pos_x = x - this.offset_x;
    this.pos_y = y - this.offset_y;
  }
}

//
// image_selector is used as an index for the selection
// of different overlays available, will be changed by user button input
// defaults to 0
//
let image_selector = 0;
let layers= [];

let currentLayer = null;

images.forEach(img => img.addEventListener('click',
  function(event)
  {
    let id = img.id;
    image_selector = parseInt(id.charCodeAt(5) - 48);
    let new_layer = new Layer (image_selector);
    layers.push(new_layer);
  }
)
);

//
// EVENT LISTENER FOR DRAG AND DROPPING LAYER
//
let is_dragging = false;

let mousex = 0;
let mousey = 0;

function grab_layer (layer) {
  is_dragging = true;
  currentLayer = layer;
  currentLayer.offset_x = event.offsetX - currentLayer.pos_x;
  currentLayer.offset_y = event.offsetY - currentLayer.pos_y;
}

canvas_feed.addEventListener("mousedown",
  function(event) {
    if (event.which == 1 ) {
      layers.forEach(
        function(layer) {
          if (layer.is_colliding(event.offsetX, event.offsetY)) {
            grab_layer(layer);
            return ;
          }
        }
      );
    } else if (event.which == 3) {
      layers.slice().reverse().forEach(
        function(layer, index, arr) {
          if (layer.is_colliding(event.offsetX, event.offsetY)) {
            layers.splice(arr.length - 1 - index, 1);
          }
        }
      );

    }
  }
);

canvas_feed.addEventListener('mouseup', function(event) {
  if (is_dragging) {
    is_dragging = false;
    if (currentLayer) {
      layers.push(currentLayer);
    }
    currentLayer = null;
  }
});

canvas_feed.addEventListener('mousemove', function(event){
  mousex = event.offsetX;
  mousey = event.offsetY;
});

function step() {
  if (streaming == true) {
    reset(source);
    if (is_dragging) {
      if (currentLayer) {
        currentLayer.center_on(mousex, mousey);
      }
    }
    layers.forEach(
      function(layer) {
        layer.drawlayer(canvas_feed_ctx);
      }
    );
  }
  requestAnimationFrame(step);
}

requestAnimationFrame(step);

//
// show && hide button savepicture
//
function show_button() {
  savepic.style.display = 'flex';
}

function hide_button() {
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
// used to reset background to video feed
let resetfeed = document.getElementById('resetfeed');

resetfeed.addEventListener('click',
  function(ev) {
    source = video;
  },
  false);

//
// Capture a photo by fetching the current contents of the video
// and drawing it into a canvas, then converting that to a PNG
// format data URL.
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
  function(ev) {
    takepicture();
    ev.preventDefault();
    show_button();
  },
  false);

//
// This part handles the upload of an image through the file input
// and the setting of the bacground to that image so that video no longer feeds
// canvas with background animation
//
function handle_image(e) {
  let reader = new FileReader();

  reader.onload = function(event) {
    let img = new Image();
    source = img;

    img.onload = function() {
      canvas_saved.width = renderWidth;
      canvas_saved.height = renderHeight;
      layers.forEach(
        function(layer) {
          layer.drawlayer(canvas_feed_ctx);
        }
      );
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

function data_to_hidden_form(e) {
  let pic = canvas_saved.toDataURL();
  let token = document.getElementsByName('token');

  sendPictureToPhp(pic, token[0].value);
}

savepic.addEventListener('click', data_to_hidden_form, false);
