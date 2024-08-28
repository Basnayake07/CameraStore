$(document).ready(function () {
  $("#search").keyup(function () {
    var input = $(this).val();
    //used for debugging purposes
    //alert(input);

    if (input != "") {
      $.ajax({
        url: "dispatchSearch.php",
        method: "GET",
        data: { input: input },

        success: function (data) {
          $("#search-result").html(data).show();
          $("#table").hide();
        },
      });
    } else {
      $("#search-result").hide();
      $("#table").show();
    }
  });
});
