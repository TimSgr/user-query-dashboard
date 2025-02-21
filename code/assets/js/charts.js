document.addEventListener('DOMContentLoaded', function(){
  var xValues = ["Andere", "Postleitzahl"];

  let all_searches = document.querySelector("#full_search_amount").textContent;
  let postcode_searches = document.querySelector("#postcode_search_amount").textContent;
  let other_searches = all_searches - postcode_searches;

  var yValues = [
    other_searches,
    postcode_searches
  ]
  var barColors = [
    "#b91d47",
    "#00aba9",
  ];

  new Chart("myChart", {
    type: "pie",
    data: {
      labels: xValues,
      datasets: [{
        backgroundColor: barColors,
        data: yValues
      }]
    },

  });
});