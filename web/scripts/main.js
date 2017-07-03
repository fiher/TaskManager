$(document).ready(function() {
	var showChar = 330;
	var ellipsestext = "...";
	var moretext = "More";
	var lesstext = "Less";
	$('.more').each(function() {
		var content = $(this).html();

		if(content.length > showChar) {

			var c = content.substr(0, showChar);
			var h = content.substr(showChar-0, content.length - showChar);

			var html = c + '<span class="moreelipses">'+ellipsestext+'</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="morelink">'+moretext+'</a></span>';

			$(this).html(html);
		}

	});

	$(".morelink").click(function(){
		if($(this).hasClass("less")) {
			$(this).removeClass("less");
			$(this).html(moretext);
		} else {
			$(this).addClass("less");
			$(this).html(lesstext);
		}
		$(this).parent().prev().toggle();
		$(this).prev().toggle();
		return false;
	});
});


$(document).ready(function() {
	$(".fancybox").fancybox({
		prevEffect	: 'fade',
		nextEffect	: 'fade',
		openEffect	: 'fade',
		closeEffect	: 'fade'
	});
});	

/* popup for button reject */
								
function btnShowPopup() {
	$(this).css("display", "block");
}
/*
function btnHidePopup() {
	$('#mypopup').css("display", "none");
}
window.onclick = function(event) {
if (event.target == popup) {
	$('#mypopup').css("display", "none");
    }
}


/*

function validate() {
    var errorNode = this.parentNode.querySelector( ".error" ),
        span = document.createElement( "span" )
    
    this.classList.remove( "invalid" );
    if ( errorNode ) {
        errorNode.parentNode.removeChild( errorNode );
    }

    if ( !this.validity.valid ) {
        this.classList.add( "invalid" );
        this.parentNode.appendChild( span );
        span.classList.add( "error" );
        span.innerHTML = this.getAttribute(
        this.validity.valueMissing ? "data-required-message" : "data-type-message" );
    }
};

var form = document.querySelector( "form" ),
    inputs = form.querySelectorAll( "textarea" );

for ( var i = 0; i < inputs.length; i++ ) {
    inputs[ i ].addEventListener( "blur", validate );
    inputs[ i ].addEventListener( "invalid", validate );
};

// Turn off the bubbles
form.addEventListener( "invalid", function( event ) {
    event.preventDefault();
}, true );

// Support: Safari, iOS Safari, default Android browser
document.querySelector( "form" ).addEventListener( "submit", function( event ) {
    if ( this.checkValidity() ) {
        alert( "Successful submission" );
    } else {
        event.preventDefault();
    }
});

*/



