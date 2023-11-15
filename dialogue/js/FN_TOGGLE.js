function FN_TOGGLE(css_id){
    var css = document.getElementById(css_id);
    if(css == null){
      console.log("toggle: " + css_id + " does not exist.");
      alert("toggle: " + css_id + " does not exist.");
      return;
    }
    if(css.style.display == 'none'){
        css.style.display = 'block';
    }else{
        css.style.display = 'none';
    }
}