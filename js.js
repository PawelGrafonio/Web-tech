$(document).ready(function() {

    // Check for hash value in URL
    $('#nav li a').click(function(){
      debugger;
      var href = $(this).attr('href');
      $('#content').load(href);
    $('#content').hide('fast',loadContent);
    $('#load').remove();
    $('#wrapper').append('<span id="load">LOADING...</span>');
    $('#load').fadeIn('normal'); 
    function loadContent() {
    	$('#content').load(toLoad,'',showNewContent())
    }
    function showNewContent() {
    	$('#content').show('normal',hideLoader());
    }
    function hideLoader() {
    	$('#load').fadeOut('normal');
    }
    return false;

    });
});
