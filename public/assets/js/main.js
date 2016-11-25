function emailNoExiste(email){
	var pattern = /^[a-z][\w.-]+@\w[\w.-]+\.[\w.-]*[a-z][a-z]$/i;
	return email.match(pattern);
}

function cargarSelectAgentes(id_select,id_select_destino){
    var id = $(id_select).val();
    
    $(".modal-loading").modal();
    
    $.ajax({
       type:'POST',
       url:basepath_javascript+"/usuarios/campania/getAgents",
       data:{id:id},
       success:function(html){
           $(".modal-loading").modal('hide');
           var opts = html.result,
               select_html;
           select_html = "";
           for(var i in opts){
               select_html +="<option value='"+i+"'>"+opts[i]+"</option>";
           }
           $(id_select_destino).html(select_html);
       },
       error: function(){
        $(".modal-loading").modal('hide');
       }
    });
}

function getMesNombre(mes){
   var month;
   switch (mes) {
    case 0:
        month = "Jan";
        break;
    case 1:
        month = "Feb";
        break;
    case 2:
        month = "Mar";
        break;
    case 3:
        month = "Apr";
        break;
    case 4:
        month = "May";
        break;
    case 5:
        month = "Jun";
        break;
    case 6:
        month = "Jul";
        break;
    case 7:
        month = "Aug";
        break;
    case 8:
        month = "Sep";
        break;
    case 9:
        month = "Oct";
        break;
    case 10:
        month = "Nov";
        break;
    case 11:
        month = "Dec";
        break;
    } 
    return month;
}