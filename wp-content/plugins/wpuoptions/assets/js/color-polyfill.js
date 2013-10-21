jQuery(function($){
  if (!Modernizr.inputtypes.color) {
    var makeHexCode = function (r, g, b) {
      var nR = r.toString(16),
	  nG = g.toString(16),
	  nB = b.toString(16);
      while (nR.length < 2) {
	nR = "0" + nR;
      }
      while (nG.length < 2) {
	nG = "0" + nG;
      }
      while (nB.length < 2) {
	nB = "0" + nB;
      }
      return "#" + nR + nG + nB;
    };

    var RGBtoHSV = function (r, g, b) {
      r = r/255, g = g/255, b = b/255;
      var max = Math.max(r, g, b), min = Math.min(r, g, b);
      var h, s, v = max;

      var d = max - min;
      s = max == 0 ? 0 : d / max;

      if (max == min) {
	h = 0;
      } else {
	switch(max){
	  case r: h = (g - b) / d + (g < b ? 6 : 0); break;
	  case g: h = (b - r) / d + 2; break;
	  case b: h = (r - g) / d + 4; break;
	}
	h /= 6;
      }

      return [Math.round(h * 360), Math.round((s * 100) * 10)/10, Math.round((v * 100) * 10)/10];
    };

    var HSVtoRGB = function (h, s, v) {
      h = h/360, s = s/100, v = v/100;
      var r, g, b;

      var i = Math.floor(h * 6);
      var f = h * 6 - i;
      var p = v * (1 - s);
      var q = v * (1 - f * s);
      var t = v * (1 - (1 - f) * s);

      switch(i % 6){
	case 0: r = v, g = t, b = p; break;
	case 1: r = q, g = v, b = p; break;
	case 2: r = p, g = v, b = t; break;
	case 3: r = p, g = q, b = v; break;
	case 4: r = t, g = p, b = v; break;
	case 5: r = v, g = p, b = q; break;
      }

      return [Math.round(r * 255), Math.round(g * 255), Math.round(b * 255)];
    };

    var sanitizeChannelValue = function (max) {
      if (/^\d+$/.test(this.value)) {
	if (parseInt(this.value, 10) > max) {
	  $(this).val(max);
	} else if (parseInt(this.value, 10) < 0) {
	  $(this).val(0);
	}
      } else if (/^[a-fA-F\d]{2}$/.test(this.value)) {
	$(this).val(parseInt(this.value, 16));
      } else {
	$(this).val(0);
      }
    };

    $('input[type="color"]').each(function(index) {
      var startValue = $(this).attr('value') || '#000000';
      var startRed = parseInt(startValue.substring(1, 3), 16);
      var startGreen = parseInt(startValue.substring(3, 5), 16);
      var startBlue = parseInt(startValue.substring(5, 7), 16);
      var startHSV = RGBtoHSV(startRed, startGreen, startBlue);

      var hiddenField = document.createElement('input');
      $(hiddenField).attr({
        type: "hidden",
        name: $(this).attr('name'),
        value: startValue
      });

      var btnContainer = document.createElement('span');
      $(btnContainer).addClass("color-picker-button-container");

      var colorBtn = document.createElement('button');
      var colorSwatch = document.createElement('div');
      colorBtn.appendChild(colorSwatch);
      $(colorBtn).addClass("color-picker-button");
      colorSwatch.style.backgroundColor = startValue;
      $(this).replaceWith(hiddenField);
      btnContainer.appendChild(colorBtn);
      $(btnContainer).insertAfter(hiddenField);

      var pickerDiv = document.createElement('div');
      $(pickerDiv).addClass("color-picker-dialog");
      pickerDiv.style.display = 'none';

      var swatchDiv = document.createElement('div');
      $(swatchDiv).addClass("color-swatch-container");

      var bigSwatch = document.createElement('div');
      $(bigSwatch).addClass("color-swatch");
      bigSwatch.style.backgroundColor = startValue;

      var sliderList = document.createElement('ul');

      var hueLI = document.createElement('li');
      $(hueLI).addClass("hue-slider-li");
      var hueSlider = document.createElement('div');
      $(hueSlider).addClass("hue-color-slider");
      var hueInput = document.createElement('input');
      $(hueInput).attr({
        type: 'text',
        size: '3',
        value: startHSV[0]
      });
      hueLI.appendChild(hueSlider);
      hueLI.appendChild(hueInput);
      sliderList.appendChild(hueLI);

      var satLI = document.createElement('li');
      $(satLI).addClass("sat-slider-li");
      var satSlider = document.createElement('div');
      $(satSlider).addClass("sat-color-slider");
      var satInput = document.createElement('input');
      $(satInput).attr({
        type: 'text',
        size: '3',
        value: startHSV[1]
      });
      satLI.appendChild(satSlider);
      satLI.appendChild(satInput);
      sliderList.appendChild(satLI);

      var valLI = document.createElement('li');
      $(valLI).addClass("val-slider-li");
      var valSlider = document.createElement('div');
      $(valSlider).addClass("val-color-slider");
      var valInput = document.createElement('input');
      $(valInput).attr({
        type: 'text',
        size: '3',
        value: startHSV[2]
      });
      valLI.appendChild(valSlider);
      valLI.appendChild(valInput);
      sliderList.appendChild(valLI);

      var redLI = document.createElement('li');
      $(redLI).addClass("red-slider-li");
      var redSlider = document.createElement('div');
      $(redSlider).addClass("red-color-slider");
      var redInput = document.createElement('input');
      $(redInput).attr({
        type: 'text',
        size: '3',
        value: startRed
      });
      redLI.appendChild(redSlider);
      redLI.appendChild(redInput);
      sliderList.appendChild(redLI);

      var greenLI = document.createElement('li');
      $(greenLI).addClass("green-slider-li");
      var greenSlider = document.createElement('div');
      $(greenSlider).addClass("green-color-slider");
      var greenInput = document.createElement('input');
      $(greenInput).attr({
        type: 'text',
        size: '3',
        value: startGreen
      });
      greenLI.appendChild(greenSlider);
      greenLI.appendChild(greenInput);
      sliderList.appendChild(greenLI);

      var blueLI = document.createElement('li');
      $(blueLI).addClass("blue-slider-li");
      var blueSlider = document.createElement('div');
      $(blueSlider).addClass("blue-color-slider");
      var blueInput = document.createElement('input');
      $(blueInput).attr({
        type: 'text',
        size: '3',
        value: startBlue
      });
      blueLI.appendChild(blueSlider);
      blueLI.appendChild(blueInput);
      sliderList.appendChild(blueLI);

      var hexInput = document.createElement('input');
      $(hexInput).attr({
        type: 'text',
        size: '7',
        value: startValue
      });
      var okButton = document.createElement('button');
      $(okButton).text('OK');
      $(okButton).addClass('color-picker-ok-button');
      pickerDiv.appendChild(swatchDiv);
      swatchDiv.appendChild(bigSwatch);
      swatchDiv.appendChild(hexInput);
      pickerDiv.appendChild(sliderList);
      pickerDiv.appendChild(okButton);
      btnContainer.appendChild(pickerDiv);
      $(hueSlider).slider({
        orientation: "horizontal",
        min: 0,
        max: 360,
        range: "min",
        value: $(hueInput).val()
      });
      $(satSlider).slider({
        orientation: "horizontal",
        min: 0,
        max: 100,
        step: 0.1,
        range: "min",
        value: $(satInput).val()
      });
      $(valSlider).slider({
        orientation: "horizontal",
        min: 0,
        max: 100,
        step: 0.1,
        range: "min",
        value: $(valInput).val()
      });
      $(redSlider).slider({
        orientation: "horizontal",
        min: 0,
        max: 255,
        range: "min",
        value: $(redInput).val()
      });
      $(greenSlider).slider({
        orientation: "horizontal",
        min: 0,
        max: 255,
        range: "min",
        value: $(greenInput).val()
      });
      $(blueSlider).slider({
        orientation: "horizontal",
        min: 0,
        max: 255,
        range: "min",
        value: $(blueInput).val()
      });

      var HSVinputchange = function() {
        var RGBcolor = HSVtoRGB(parseInt($(hueInput).val(), 10), parseFloat($(satInput).val()), parseFloat($(valInput).val()));
        var newColor = makeHexCode(RGBcolor[0], RGBcolor[1], RGBcolor[2]);
        $(redSlider).slider("option", "value", RGBcolor[0]);
        $(greenSlider).slider("option", "value", RGBcolor[1]);
        $(blueSlider).slider("option", "value", RGBcolor[2]);
        $(redInput).val(RGBcolor[0]);
        $(greenInput).val(RGBcolor[1]);
        $(blueInput).val(RGBcolor[2]);
        $(hexInput).val(newColor);
        bigSwatch.style.backgroundColor = newColor;
        colorSwatch.style.backgroundColor = newColor;
        $(hiddenField).val(newColor.toLowerCase());
      };
      var RGBinputchange = function() {
        var HSVcolor = RGBtoHSV(parseInt($(redInput).val(), 10), parseInt($(greenInput).val(), 10), parseInt($(blueInput).val(), 10));
        var newColor = makeHexCode(parseInt($(redInput).val(), 10), parseInt($(greenInput).val(), 10), parseInt($(blueInput).val(), 10));
        $(hueSlider).slider("option", "value", HSVcolor[0]);
        $(satSlider).slider("option", "value", HSVcolor[1]);
        $(valSlider).slider("option", "value", HSVcolor[2]);
        $(hueInput).val(HSVcolor[0]);
        $(satInput).val(HSVcolor[1]);
        $(valInput).val(HSVcolor[2]);
        $(hexInput).val(newColor);
        bigSwatch.style.backgroundColor = newColor;
        colorSwatch.style.backgroundColor = newColor;
        $(hiddenField).val(newColor.toLowerCase());
      };
      var HSVslide = function() {
        var RGBcolor = HSVtoRGB(parseInt($(hueInput).val(), 10), parseInt($(satInput).val(), 10), parseInt($(valInput).val(), 10));
        var newColor = makeHexCode(RGBcolor[0], RGBcolor[1], RGBcolor[2]);
        $(redSlider).slider("option", "value", RGBcolor[0]);
        $(greenSlider).slider("option", "value", RGBcolor[1]);
        $(blueSlider).slider("option", "value", RGBcolor[2]);
        $(redInput).val(RGBcolor[0]);
        $(greenInput).val(RGBcolor[1]);
        $(blueInput).val(RGBcolor[2]);
        bigSwatch.style.backgroundColor = newColor;
        $(hexInput).val(newColor);
      };
      var HSVslidechange = function() {
        var RGBcolor = HSVtoRGB(parseInt($(hueInput).val(), 10), parseInt($(satInput).val(), 10), parseInt($(valInput).val(), 10));
        var newColor = makeHexCode(RGBcolor[0], RGBcolor[1], RGBcolor[2]);
        $(redSlider).slider("option", "value", RGBcolor[0]);
        $(greenSlider).slider("option", "value", RGBcolor[1]);
        $(blueSlider).slider("option", "value", RGBcolor[2]);
        $(redInput).val(RGBcolor[0]);
        $(greenInput).val(RGBcolor[1]);
        $(blueInput).val(RGBcolor[2]);
        bigSwatch.style.backgroundColor = newColor;
        $(hexInput).val(newColor);
        colorSwatch.style.backgroundColor = newColor;
        $(hiddenField).val(newColor.toLowerCase());
      };
      var RGBslide = function() {
        var HSVcolor = RGBtoHSV(parseInt($(redInput).val(), 10), parseInt($(greenInput).val(), 10), parseInt($(blueInput).val(), 10));
        var newColor = makeHexCode(parseInt($(redInput).val(), 10), parseInt($(greenInput).val(), 10), parseInt($(blueInput).val(), 10));
        $(hueSlider).slider("option", "value", HSVcolor[0]);
        $(satSlider).slider("option", "value", HSVcolor[1]);
        $(valSlider).slider("option", "value", HSVcolor[2]);
        $(hueInput).val(HSVcolor[0]);
        $(satInput).val(HSVcolor[1]);
        $(valInput).val(HSVcolor[2]);
        $(hexInput).val(newColor);
        bigSwatch.style.backgroundColor = newColor;
      };
      var RGBslidechange = function() {
        var HSVcolor = RGBtoHSV(parseInt($(redInput).val(), 10), parseInt($(greenInput).val(), 10), parseInt($(blueInput).val(), 10));
        var newColor = makeHexCode(parseInt($(redInput).val(), 10), parseInt($(greenInput).val(), 10), parseInt($(blueInput).val(), 10));
        $(hueSlider).slider("option", "value", HSVcolor[0]);
        $(satSlider).slider("option", "value", HSVcolor[1]);
        $(valSlider).slider("option", "value", HSVcolor[2]);
        $(hexInput).val(newColor);
        $(hueInput).val(HSVcolor[0]);
        $(satInput).val(HSVcolor[1]);
        $(valInput).val(HSVcolor[2]);
        bigSwatch.style.backgroundColor = newColor;
        colorSwatch.style.backgroundColor = newColor;
        $(hiddenField).val(newColor.toLowerCase());
      };

      $(hexInput).change(function () {
        if (/^[a-fA-F\d]{6}$/.test(this.value)) {
          $(this).val("#" + this.value);
        }
        if (/^#[a-fA-F\d]{6}$/.test(this.value)) {
          var redVal = parseInt(this.value.substring(1, 3), 16);
          var greenVal = parseInt(this.value.substring(3, 5), 16);
          var blueVal = parseInt(this.value.substring(5, 7), 16);
          var HSVcolor = RGBtoHSV(redVal, greenVal, blueVal);
          bigSwatch.style.backgroundColor = this.value;
          $(hueInput).val(HSVcolor[0]);
          $(satInput).val(HSVcolor[1]);
          $(valInput).val(HSVcolor[2]);
          $(redInput).val(redVal);
          $(greenInput).val(greenVal);
          $(blueInput).val(blueVal);
          $(hueSlider).slider("option", "value", HSVcolor[0]);
          $(satSlider).slider("option", "value", HSVcolor[1]);
          $(valSlider).slider("option", "value", HSVcolor[2]);
          $(redSlider).slider("option", "value", redVal);
          $(greenSlider).slider("option", "value", greenVal);
          $(blueSlider).slider("option", "value", blueVal);
          colorSwatch.style.backgroundColor = this.value;
          $(hiddenField).val(this.value.toLowerCase());
        } else {
          $(this).val(makeHexCode(parseInt($(redInput).val(), 10), parseInt($(greenInput).val(), 10), parseInt($(blueInput).val(), 10)));
        }
      });
      $(hueInput).change(function () {
        sanitizeChannelValue.call(this, 360);
        $(hueSlider).slider("option", "value", parseInt(this.value, 10));
        HSVinputchange();
      });
      $(satInput).change(function () {
        sanitizeChannelValue.call(this, 100);
        $(satSlider).slider("option", "value", parseInt(this.value, 10));
        HSVinputchange();
      });
      $(valInput).change(function () {
        sanitizeChannelValue.call(this, 100);
        $(valSlider).slider("option", "value", parseInt(this.value, 10));
        HSVinputchange();
      });
      $(redInput).change(function () {
        sanitizeChannelValue.call(this, 255);
        $(redSlider).slider("option", "value", parseInt(this.value, 10));
        RGBinputchange();
      });
      $(greenInput).change(function () {
        sanitizeChannelValue.call(this, 255);
        $(greenSlider).slider("option", "value", parseInt(this.value, 10));
        RGBinputchange();
      });
      $(blueInput).change(function () {
        sanitizeChannelValue.call(this, 255);
        $(blueSlider).slider("option", "value", parseInt(this.value, 10));
        RGBinputchange();
      });
      $(hueSlider).bind({
        slide: function(event, ui) {
          if (event.originalEvent !== undefined) {
            $(hueInput).val($(this).slider("value"));
            HSVslide();
          }
        },
        slidechange: function(event, ui) {
          if (event.originalEvent !== undefined) {
            $(hueInput).val($(this).slider("value"));
            HSVslidechange();
          }
        }
      });
      $(satSlider).bind({
        slide: function(event, ui) {
          if (event.originalEvent !== undefined) {
            $(satInput).val($(this).slider("value"));
            HSVslide();
          }
        },
        slidechange: function(event, ui) {
          if (event.originalEvent !== undefined) {
            $(satInput).val($(this).slider("value"));
            HSVslidechange();
          }
        }
      });
      $(valSlider).bind({
        slide: function(event, ui) {
          if (event.originalEvent !== undefined) {
            $(valInput).val($(this).slider("value"));
            HSVslide();
          }
        },
        slidechange: function(event, ui) {
          if (event.originalEvent !== undefined) {
            $(valInput).val($(this).slider("value"));
            HSVslidechange();
          }
        }
      });
      $(redSlider).bind({
        slide: function(event, ui) {
          if (event.originalEvent !== undefined) {
            $(redInput).val($(this).slider("value"));
            RGBslide();
          }
        },
        slidechange: function(event, ui) {
          if (event.originalEvent !== undefined) {
            $(redInput).val($(this).slider("value"));
            RGBslidechange();
          }
        }
      });
      $(greenSlider).bind({
        slide: function(event, ui) {
          if (event.originalEvent !== undefined) {
            $(greenInput).val($(this).slider("value"));
            RGBslide();
          }
        },
        slidechange: function(event, ui) {
          if (event.originalEvent !== undefined) {
            $(greenInput).val($(this).slider("value"));
            RGBslidechange();
          }
        }
      });
      $(blueSlider).bind({
        slide: function(event, ui) {
          if (event.originalEvent !== undefined) {
            $(blueInput).val($(this).slider("value"));
            RGBslide();
          }
        },
        slidechange: function(event, ui) {
          if (event.originalEvent !== undefined) {
            $(blueInput).val($(this).slider("value"));
            RGBslidechange();
          }
        }
      });
      if (Modernizr.csstransitions) {
	pickerDiv.className = "color-picker-dialog color-picker-closed";
	$(colorBtn).click(function () {
	  $(pickerDiv).unbind('transitionend');
	  $(pickerDiv).unbind('oTransitionEnd');
	  $(pickerDiv).unbind('webkitTransitionEnd');
	  $(pickerDiv).unbind('MSTransitionEnd');
	  pickerDiv.style.display = 'block';
	  $(pickerDiv).css('opacity');
	  pickerDiv.className = "color-picker-dialog color-picker-open";
	  return false;
	});
	var closeFunc = function () {
	  if (pickerDiv.className == "color-picker-dialog color-picker-open") {
	    var transitionend_function = function(event, ui) {
	      pickerDiv.style.display = 'none';
	      $(pickerDiv).unbind({
		transitionend: transitionend_function,
		oTransitionEnd: transitionend_function,
		webkitTransitionEnd: transitionend_function,
		MSTransitionEnd: transitionend_function
	      });
	    }
	    $(pickerDiv).bind({
	      transitionend: transitionend_function,
	      oTransitionEnd: transitionend_function,
	      webkitTransitionEnd: transitionend_function,
	      MSTransitionEnd: transitionend_function
	    });
	    pickerDiv.className = "color-picker-dialog color-picker-closed";
	    return false;
	  }
	}
	$(pickerDiv).mouseleave(closeFunc);
	$(okButton).click(closeFunc);
      } else {
	$(colorBtn).click(function () {
	  $(pickerDiv).fadeIn();
	  return false;
	});
	var closeFunc = function () {
	  $(pickerDiv).fadeOut();
	  return false;
	}
	$(pickerDiv).mouseleave(closeFunc);
	$(okButton).click(closeFunc);
      }
    });
  }
});
