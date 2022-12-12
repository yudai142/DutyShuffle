let url = new URL(window.location.href);
let params = url.searchParams;

function redirectValue() {
  let month = document.getElementById('date');
  location.href = "?ym=" + month.value;
}

let month = document.getElementById('date');

if(params.get('ym')){
  month.value = params.get('ym');
}else{
  let today = new Date();
  today.setDate(today.getDate());
  let yyyy = today.getFullYear();
  let mm = ("0"+(today.getMonth()+1)).slice(-2);
  let dd = ("0"+today.getDate()).slice(-2);
  month.value = yyyy+'-'+mm+'-'+dd;
}

month.addEventListener('change', redirectValue);
