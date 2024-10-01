//JQuery Module Pattern

// An object literal
var app = {
  init: function() {
    app.functionOne();
  },
  functionOne: function () {
  }
};


// Check if an element has a specific data attribute
jQuery.fn.hasDataAttr = function(name) {
  return $(this)[0].hasAttribute('data-'+ name);
};


// Get data attribute. If element doesn't have the attribute, return default value
jQuery.fn.dataAttr = function(name, def) {
  return $(this)[0].getAttribute('data-'+ name) || def;
};


// Get target of an action from element.
  app.getTarget = function(e) {
    var target;
    if ( e.hasDataAttr('target') ) {
      target = e.data('target');
    }
    else {
      target = e.attr('href');
    }

    if ( target == 'next' ) {
      target = $(e).next();
    }
    else if ( target == 'prev' ) {
      target = $(e).prev();
    }

    if ( target == undefined ) {
      return false;
    }

    return target;
  };


$("document").ready(function () {
  app.init();
  sidebar.init();
  quickview.init();
  //dock.init();
  aside.init();
});

$(function () {
	$('[data-toggle="tooltip"]').tooltip();
	$('.scrollable').perfectScrollbar();
  $('.ui.dropdown').dropdown();
})
