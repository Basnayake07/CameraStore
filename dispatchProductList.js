$(document).ready(function () {
  //function to fetch products according to the user typing
  $("#product-name").keyup(function () {
    var query = $(this).val();
    if (query.length > 0) {
      $.ajax({
        url: "dispatchFetchProducts.php",
        method: "POST",
        data: { query: query },
        success: function (data) {
          $("#product-list").fadeIn(); //show product list
          $("#product-list").html(data); //insert data into the product list
        },
      });
    } else {
      $("product-list").fadeOut(); //if input is empty, hide the list
    }
  });
});

//handle click event
$(document).on("click", ".product-list-item", function () {
  $("#product-name").val($(this).text()); // fill input filed with the clicked item
  $("#product-list").fadeOut(); // hide the list
});
