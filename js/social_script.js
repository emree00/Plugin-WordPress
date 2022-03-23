jQuery(document).ready(function($) {

  $.ajax({
    type: 'GET',
    url: ajax_object.ajaxurl + 'socialPosts',
    date: {
      data: [],
      action: 'action-name',
      nonce: ajax_object.nonce
    },
    success: function(response) {
      $('.wh_social_container').append(response);
      $('.grid').colcade({
        columns: '.grid-col',
        items: '.grid-item'
      });


      $('.social-menu').click(function() {
        var parentDiv = $(this).closest(".dropdown"); //find the parent div holding <i> as well as dropdown-content
        parentDiv.find('.dropdown-content').toggleClass('show'); //target the exact dropdown-content
      });

      // Close the dropdown if the user clicks outside of it
      $(window).click(function(event) {
        if (!$(event.target).is('.social-menu')) {
            if ($(".dropdown-content").hasClass('show')) {
              $(".dropdown-content").removeClass('show');
            }
          }
        });
    }
  });
});
