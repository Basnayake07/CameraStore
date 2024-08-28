$(document).ready(function () {
  //function to fetch products according to the user typing
  $("#store-name").keyup(function () {
    var query = $(this).val();
    if (query.length > 0) {
      $.ajax({
        url: "dispatchFetchStores.php",
        method: "POST",
        data: { query: query },
        success: function (data) {
          $("#store-list").fadeIn(); //show store list
          $("#store-list").html(data); //insert data into the store list
        },
      });
    } else {
      $("#store-list").fadeOut(); //if input is empty, hide the list
    }
  });
});

//handle click event
$(document).on("click", ".store-list-item", function () {
  $("#store-name").val($(this).text()); // fill input filed with the clicked item
  $("#store-list").fadeOut(); // hide the list
});
