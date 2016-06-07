var link = document.createElement("link");
link.setAttribute("rel", "stylesheet");
link.setAttribute("href", "../resources/lightavatars.css");
var head = document.getElementsByTagName("head")[0];
head.appendChild(link);

window.onload = function() {
    var linkvalue=document.getElementById('setting_lightavatars_link').value;
    linkvalue=linkvalue.split(' ');
    linkvalue='lavatar__link lavatar__link--'+linkvalue.join(' lavatar__link--');
    document.getElementById('la_link').className=linkvalue;
    
    var imgvalue=document.getElementById('setting_lightavatars_img').value;
    imgvalue=imgvalue.split(' ');
    imgvalue='lavatar__img lavatar__img--'+imgvalue.join(' lavatar__img--');
    document.getElementById('la_img').className=imgvalue;
    
    document.getElementById('setting_lightavatars_link').addEventListener("change", function(){
        var linkvaluechage=document.getElementById('setting_lightavatars_link').value;
        linkvaluechage=linkvaluechage.split(' ');
        linkvaluechage='lavatar__link lavatar__link--'+linkvaluechage.join(' lavatar__link--');
        document.getElementById('la_link').className=linkvaluechage;
    });

    document.getElementById('setting_lightavatars_img').addEventListener("change", function(){
        var imgvaluechage=document.getElementById('setting_lightavatars_img').value;
        imgvaluechage=imgvaluechage.split(' ');
        imgvaluechage='lavatar__img lavatar__img--'+imgvaluechage.join(' lavatar__img--');
        document.getElementById('la_img').className=imgvaluechage;
    });
    
    var selected = document.getElementById('setting_lightavatars_view');
    var optionvalue=selected.options[selected.selectedIndex].text;
    var avatarvalue=document.getElementById('setting_lightavatars_'+optionvalue).value;
    avatarvalue=avatarvalue.split(' ');
    avatarvalue='lavatar lavatar--'+avatarvalue.join(' lavatar--');
    document.getElementById('la_avatar').className=avatarvalue;
    
    selected.addEventListener("change", function(){
        optionvalue=selected.options[selected.selectedIndex].text;
        var avatarchagevalue=document.getElementById('setting_lightavatars_'+optionvalue).value;
        avatarchagevalue=avatarchagevalue.split(' ');
        avatarchagevalue='lavatar lavatar--'+avatarchagevalue.join(' lavatar--');
        document.getElementById('la_avatar').className=avatarchagevalue;
        
        document.getElementById('setting_lightavatars_'+optionvalue).addEventListener("change", function(){
            var avatarchagevalue=document.getElementById('setting_lightavatars_'+optionvalue).value;
            avatarchagevalue=avatarchagevalue.split(' ');
            avatarchagevalue='lavatar lavatar--'+avatarchagevalue.join(' lavatar--');
            document.getElementById('la_avatar').className=avatarchagevalue;
        });
    });
    document.getElementById('setting_lightavatars_'+optionvalue).addEventListener("change", function(){
        var avatarchagevalue=document.getElementById('setting_lightavatars_'+optionvalue).value;
        avatarchagevalue=avatarchagevalue.split(' ');
        avatarchagevalue='lavatar lavatar--'+avatarchagevalue.join(' lavatar--');
        document.getElementById('la_avatar').className=avatarchagevalue;
    });
};
