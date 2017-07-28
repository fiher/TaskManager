/* click for more text in description */
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

/* display image in full width */
$(document).ready(function() {
	$(".fancybox").fancybox({
		prevEffect	: 'fade',
		nextEffect	: 'fade',
		openEffect	: 'fade',
		closeEffect	: 'fade'
	});
});	


/* popup for button reject */
function btnShowPopup(id) {
	var popup = document.getElementById('popup'+id);
	$(popup).css("display", "block");
}
function btnHidePopup(id) {
	var popup = document.getElementById('popup'+id);
	$(popup).css("display", "none");
}
/*
window.onclick = function btnHidePopup() {
    if (event.target == popup) {
	    $(popup).css("display", "none");
    }
}
*/

/* popup for insert link */
function btnShowLink(id) {
	var link = document.getElementById('link'+id);
	$(link).css("display", "block");
}
function btnHideLink(id) {
	var link = document.getElementById('link'+id);
	$(link).css("display", "none");
}


/* checkbox hide and show termdate */
function HideTerm(){
	var x = document.getElementById('appbundle_project_term');
    x.setAttribute("onkeydown", "return false");
	var check = document.getElementById('appbundle_project_term');
	var isCheck = document.getElementById('appbundle_project_withoutTerm');
		if(isCheck.checked) {
        $("label[for='appbundle_project_term']").attr("id", "showterm");
        x.setAttribute("value", "2020-02-09");
		check.style.display = 'none';
	    }else{
        $("label[for='appbundle_project_term']").removeAttr("id", "showterm");
        x.removeAttribute("value", "2020-02-09");
		check.style.display = 'inline-block';
	    }
    $( isCheck ).click (function() {
	var check = document.getElementById('appbundle_project_term');
	var isCheck = document.getElementById('appbundle_project_withoutTerm');
	    if(isCheck.checked) {
        $("label[for='appbundle_project_term']").attr("id", "showterm");
        x.setAttribute("value", "2020-02-09");
		check.style.display = 'none';
	    }else{
        $("label[for='appbundle_project_term']").removeAttr("id", "showterm");
        x.removeAttribute("value", "2020-02-09");
		check.style.display = 'inline-block';
	   }
    });
}
window.onload = HideTerm;




/* scroll for comments always bottom */

var height = 0;
$('.overflow').each(function(i, value){
    height += parseInt($(this).height());
});
height += '';
$('.overflow').animate({scrollTop: height});



/* submit form after choose images */
function submitForm(id) {
   var uploadimg = document.getElementById(id);
	$(uploadimg).submit();
}



//add active class to current page button in navigation
jQuery(function($) {
    var path = window.location.href;
    $('.pull-left a').each(function() {
        if (this.href === path) {
            $(this).addClass('active');
        }
    });
});

/*
var first = $(location).attr('pathname');
var second = $(location).attr('pathname');
first = first.split("/")[1];
second = second.split("/")[2];
document.getElementById("demo").innerHTML = 
"The full URL of this page is:<br>" + window.location.host + "/" + first + "/" + second;
*/


/* ajax request */
/*
$(function () {
        $('form').on('submit', function (e) {
          e.preventDefault();

          $.ajax({
            type: 'POST',
            url: '/project/38/update',
            success: function () {
              alert('form was submitted');
            }
          });

        });

});



$('form.ajax').on('submit', function(){

   var that = $(this),
   url = that.attr('action'),
   method = that.attr('method'),
   data = {};

});
*/
/*
function chk(){	
          $.ajax({
            type: 'POST',
            url: '/project/46/update',
			data: $(this).serialize(),
            success: function () {
              alert('form was submitted');
            }
          });
		  return false;
}
*/



