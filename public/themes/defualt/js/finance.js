//checked动作;
$("#warning").click(function(){
  if(!this.checked){
    console.log($(".switch-check"))
    $(".switch-check").addClass("d-none")
  }else{
    $(".switch-check").removeClass("d-none")
  }
})
// console.log($("#warning")[0].checked)
