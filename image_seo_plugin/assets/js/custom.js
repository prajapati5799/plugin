jQuery(document).ready(function () {
  jQuery('.update_data').click(function () {

    var box_data = new Array();
    var cAlt = jQuery('.cAlt');
    var cTitle = jQuery('.cTitle');
    var box_data_input = jQuery('.box_data_input');


    for (var i = 0; i < box_data_input.length; i++) {
      box_data_input_1 = jQuery(box_data_input[i]).attr('data-attid');
      cTitledata = jQuery(cTitle[i]).val();
      caltdata = jQuery(cAlt[i]).val();
      box_data.push({
        cTitle: cTitledata,
        cAlt: caltdata,
        attid: box_data_input_1,
      });
    }

    jQuery.ajax({
      url: localajax_1.ajaxurl, // this will point to admin-ajax.php
      dataType: 'html',
      type: 'POST',
      data: {
        'action': 'update_data',
        'box_data': box_data,
      },
      success: function (response) {

        var mystructs = '<h2 class="data_not_available pass">Data sucessfully Add</h2>';
        jQuery(".data_class_1").html(mystructs);
        jQuery('#update_link').hide();

      }

    });
  });

});
