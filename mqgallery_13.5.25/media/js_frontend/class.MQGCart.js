var MQGCart = {};
MQGCart.formid = 'MQGCartform';
MQGCart.removeItem = function(number){
  if(undefined == document.getElementById('qty-'+number)) return;
  document.getElementById('qty-'+number).value = '0';
  document.getElementById(this.formid).submit();
}
