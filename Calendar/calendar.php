<?php   
ini_set("session.cookie_httponly", 1);
session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="calendar_style.css">
    <title>Calendar</title>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js" type="text/javascript"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
    let username = '<?php if (isset($_SESSION['username'])){ //assign username for either user or guest
        echo htmlentities($_SESSION['username']);
    } else {
        echo htmlentities('');
    }?>;' 
    let user_id = '';
    function login(){ //Using verify.php to check if the username and password is registered and if so, log the user in
        let credentials = {username:document.getElementById("username").value, password:document.getElementById("password").value};
        fetch("verify.php",{method:'POST', body:JSON.stringify(credentials), headers: { 'content-type': 'application/json' }})
        .then(res => res.json())
        .then(function(result){
            if (result.success==true){
                alert ("Login successful");     
                $("div.login_register").css({display:"none"});
                $("div.logout").css({display:"inline"});
                $("div.tag_names").css({display:"block"});
                username = result.username;
                $("span.title").html("Welcome "+username+"!");
                $("div.welcome").css({display:"inline"});
                $("#username").val('');
                $("#password").val('');
                refreshMonth();
            }
            else{
                alert("Login failed, "+JSON.stringify(result.message));
            }
        })
        .catch(error => console.error('Error:',error));
    }
    function register(){ //Registers a user usign create_user.php and return the buttons to login and register
        let credentials = {username:document.getElementById("username").value, password:document.getElementById("password").value};
        fetch("create_user.php",{method:'POST', body:JSON.stringify(credentials), headers: { 'content-type': 'application/json' }})
        .then(res => res.json())
        .then(function(result){
            if (result.success==true){
                alert ("Register succesful, please login");
                cancel_register();
            }
            else{
                alert("Register failed, "+JSON.stringify(result.message));
                cancel_register();
            }
        })
        .catch(error => console.error('Error:',error));
    }
    function logout(){ //kills session
        let checking = {current_id:user_id};
        fetch("logout.php",{method:'POST',body:JSON.stringify(checking)})
        .then(response=>console.log(response.json()))
        .catch(error=>console.error('Error:', error));
        username = null;
        user_id='';
        $("div.login_register").css({display:"inline"});
        $("div.welcome").css({display:"none"});
        $("div.logout").css({display:"none"});
        $("div.tag_names").css({display:"none"});
        refreshMonth();
    }
    function cancel_register(){ //change the buttons from register and cancel to login and register
        $("#username").val('');
        $("#password").val('');
        $("#username").attr("placeholder","Enter Username Here");
        $("#password").attr("placeholder","Enter Password Here");
        $("#password").attr("type","password");
        $("#create_account").remove();
        $("#cancel_register").remove();
        let login_button = document.createElement("button");
        login_button.setAttribute("id", "login");
        $("div.login_register").append(login_button);
        $("#login").html("Login");
        let register = document.createElement("button");
        register.setAttribute("id","register");
        $("div.login_register").append(register);
        $("#register").html("Register");
        $("button#login").click(login);
        $("button#register").click(register_button);
    }
    function register_button(){ //Change the buttons from login and register to 2 new buttons called register and cancel
        $("#username").attr("placeholder","New Username");
        $("#username").val('');
        $("#password").attr("placeholder","New Password");
        $("#password").attr("type","text");
        $("#password").val('');
        $("#login").remove();
        $("#register").remove();
        let register_button = document.createElement("button");
        register_button.setAttribute("id", "create_account");
        $("div.login_register").append(register_button);
        $("#create_account").html("Create an account!")
        let cancel_register_button = document.createElement("button");
        cancel_register_button.setAttribute("id","cancel_register");
        $("div.login_register").append(cancel_register_button);
        $("#cancel_register").html("Cancel");
        //Create new account button:
        $("#create_account").click(register);
        //Cancel registration, revert to login
        $("#cancel_register").click(cancel_register);
    } 
    //Calendar functions from 330 website
    (function(){Date.prototype.deltaDays=function(c){return new Date(this.getFullYear(),this.getMonth(),this.getDate()+c)};Date.prototype.getSunday=function(){return this.deltaDays(-1*this.getDay())}})();
    function Week(c){this.sunday=c.getSunday();this.nextWeek=function(){return new Week(this.sunday.deltaDays(7))};this.prevWeek=function(){return new Week(this.sunday.deltaDays(-7))};this.contains=function(b){return this.sunday.valueOf()===b.getSunday().valueOf()};this.getDates=function(){for(var b=[],a=0;7>a;a++)b.push(this.sunday.deltaDays(a));return b}}
    function Month(c,b){this.year=c;this.month=b;this.nextMonth=function(){return new Month(c+Math.floor((b+1)/12),(b+1)%12)};this.prevMonth=function(){return new Month(c+Math.floor((b-1)/12),(b+11)%12)};this.getDateObject=function(a){return new Date(this.year,this.month,a)};this.getWeeks=function(){var a=this.getDateObject(1),b=this.nextMonth().getDateObject(0),c=[],a=new Week(a);for(c.push(a);!a.contains(b);)a=a.nextWeek(),c.push(a);return c}};
    let d = 1;
    let m = 2;
    let y = 2020;
    let today_d = 1;
    let today_m = 3;
    let today_y = 2020;
    let current_element = 0;
    //let counter = 0; //this is something I'm experimenting with, it doesn't affect the code too much
    let checked_tags = "";
    let php_tags="";
    let stored_event_date;
    let stored_event_time;
    let to_div;
    let i;
    let event_name;
    let event_date;
    let event_description;
    let sess_token;
    function today(){
        let today = new Date();
        d = today.getDate();
        m = today.getMonth();
        y = today.getFullYear();
        today_d = d;
        today_m = m;
        today_y = y;
        refreshMonth();
    }
    function numtoMonth(m){
        let months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        return months[m];
    }
    function date_to_cell(d){
        d = String(d);
        d = d.split("-"); //Split date by -, so 2020-03-02 becomes an array of 2020, 03, 02
        let year = d[0];
        let month = d[1];
        let day = d[2];  
        let currentMonth = new Month(y, m);  // 2020, 2 = March 2020 (0 = January and Sunday)
        let weeks = currentMonth.getWeeks();
        for (let i = 0; i<=4; i++){
            let days = weeks[i].getDates();
            for (let j=0; j<=6; j++){
                if (days[j].getFullYear() == year && days[j].getMonth()+1 == month && days[j].getDate() == day){
                    current_element = document.getElementsByClassName(j)[i];
                    return i;   //Returns which row
                }
            }
        }
    }
    function refreshMonth(){ //Populates the table with individual dates as well as div classes for event dialogs through event_of_day()
        $("div#has_event").contents().unwrap();
        document.getElementById("month_year").textContent=numtoMonth(m)+ "  " + y;
        let currentMonth = new Month(y, m);  // 2020, 2 = March 2020 (0 = January and Sunday)
        let weeks = currentMonth.getWeeks();
        for (let i = 0; i<=4; i++){
            let days = weeks[i].getDates();
            for (let j=0; j<=6; j++){
                document.getElementsByClassName(j)[i].innerHTML="<span class = 'days'>"+days[j].getDate()+"</span>";
                //current_element = document.getElementsByClassName(j)[i];    //Classname represents day of the week, has 5 instances of it for the 5 weeks
                let current_date = new Date(days[j].getFullYear(),days[j].getMonth(),days[j].getDate());
                event_of_day(current_date,i,j);
                if (m != days[j].getMonth()){
                    document.getElementsByClassName(j)[i].innerHTML="<span class = 'others'>"+days[j].getDate()+"</span>";    //If date is a different month, format differently
                }
                if (today_d == days[j].getDate() && today_m == days[j].getMonth()&& today_y == days[j].getFullYear()){
                    document.getElementsByClassName(j)[i].innerHTML="<span class = 'today'>"+days[j].getDate()+"</span>";    //If it is today, format differently
                }
            }
        }
    }
    function wrap(element, wrapper) {
        element.parentNode.insertBefore(wrapper, element);
        wrapper.appendChild(element);
    }
    function event_of_day(current_day,a,b){ //Checks if there is a 
        fetch("check_login.php",{method:'POST'})
        .then(res => res.json())
        .then(function(result){
            if (result.message=='true'){
                user_id = result.id;
                $("#add_Event").css({display:"inline"});
                $("div.tag_names").css({display:"block"});
                sess_token = result.token;
                let php_day = {'date':current_day.toISOString().split('T')[0], 'userid': user_id, 'token':sess_token}; //user_id can be only modified to be equal to the session id or nothign. Abuse of functionality is again checked during the individual php files where the input id will be checked against the id associated with the session username
                get_events(current_day,a,b, php_day);
            }
            else{
                user_id = null;
                $("#add_Event").css({display:"none"});
                $("div.tag_names").css({display:"none"});
            }
            // sess_token = result.token;
            // let php_day = {'date':current_day.toISOString().split('T')[0], 'userid': user_id, 'token':sess_token};
            // get_events(current_day,a,b, php_day);
        })
        .catch(error => console.error('Error:',error));
    }
    function get_events(current_day,a,b, php_day){ //uses get_event.php to get event information on a specific date, compile them into a div class, and append the div class to the particular cell in table depending on the filter(tags)
        // if (php_day.userid=="){
            fetch("get_event.php",{method:'POST',body:JSON.stringify(php_day), headers:{'content-type': 'application/json' }})
            .then(res=>res.json())
            .then(function(result){
                if (result.event_names.length!=0){
                    let stored_tag = result.event_tags;
                    let stored_events = result.event_names;
                    let stored_event_d= result.event_descriptions;
                    let stored_event_id = result.event_id;
                    let time = result.time;
                    stored_event_date = result.date[0];
                    let tags;
                    let to_insert =false;
                    to_div = document.createElement("div");
                    i = date_to_cell(stored_event_date);
                    to_div.title="Events of the Day";
                    to_div.setAttribute("class","hello");
                    for (let k =0; k<result.event_names.length;k++){
                        // to_div.setAttribute("id","event"); 
                        if (stored_tag[k]!=null){
                            tags = stored_tag[k].split(",");
                        }
                        else{
                            tags=null;
                        }
                        if (checked_tags!=""){ //for loops checking if the event is eligible to be inserted based on the current selected tags
                            if (tags!=null){
                                for (let counter_3 = 0; counter_3<checked_tags.length-1;counter_3++){
                                    for (counter_4=0;counter_4<tags.length-1;counter_4++){
                                        if(checked_tags[counter_3]==tags[counter_4]){
                                            to_insert = true;
                                        }
                                    }
                                }
                            }
                        }
                        else{
                            to_insert = true;
                        }
                        if (to_insert){
                            let name_label = document.createElement("label");
                            name_label.innerHTML = "Event Name:";
                            name_label.setAttribute("class","event_name_label");
                            let desc_label = document.createElement("label");
                            desc_label.innerHTML = "Event Description:";
                            desc_label.setAttribute("class","event_desc_label");
                            let time_label = document.createElement("label");
                            time_label.innerHTML = "Event Time:";
                            time_label.setAttribute("class","event_time_label");
                            time_label.setAttribute("id",stored_event_id[k]);
                            let to_insert_name = document.createElement("p");
                            to_insert_name.innerHTML = stored_events[k];
                            let to_insert_des= document.createElement("p");
                            to_insert_des.innerHTML = stored_event_d[k];
                            let to_insert_time= document.createElement("p");
                            to_insert_time.innerHTML = time[k];
                            let to_insert_date= document.createElement("p");
                            to_insert_date.innerHTML = result.date[k];
                            to_insert_name.setAttribute("class", "event_name");
                            to_insert_name.setAttribute("id", stored_event_id[k]);
                            to_insert_des.setAttribute("class", "event_description");
                            to_insert_des.setAttribute("id", stored_event_id[k]);
                            to_insert_time.setAttribute("class", "event_time");
                            to_insert_time.setAttribute("id", stored_event_id[k]);
                            to_insert_date.setAttribute("class", "event_date");
                            to_insert_date.setAttribute("id", stored_event_id[k]);
                            //Inserting elements to table cell
                            to_div.appendChild(name_label);
                            to_div.appendChild(to_insert_name);
                            to_div.innerHTML += "<br>";
                            to_div.appendChild(desc_label);
                            to_div.appendChild(to_insert_des);
                            to_div.innerHTML += "<br>";
                            to_div.appendChild(time_label);
                            to_div.appendChild(to_insert_time);
                            to_div.innerHTML += "<br>";
                            to_div.appendChild(to_insert_date); //Inserts date, but in CSS we will make the date display:none. This is so in the future we can more easily grab the date when editing
                            let current_tags = document.getElementsByClassName("choose_tags")[0].children;
                            let tags_text = document.createElement("div");
                            tags_text.setAttribute("class","text_tags");
                            tags_text.setAttribute("id", stored_event_id[k]);
                            tags_text.innerHTML+="Tags: ";
                            let choose_radio = document.createElement("div"); //this part of the code adds radio buttons for selecting tags during edit event as well as a piece of text that outlines all of the tags of the event
                            choose_radio.setAttribute("class","choose_tags");
                            choose_radio.setAttribute("id",stored_event_id[k]);
                            let choose_labels = document.createElement("div");
                            choose_labels.setAttribute("class","choose_names");
                            choose_labels.setAttribute("id",stored_event_id[k]);
                            for (let counter_3 = 0; counter_3<current_tags.length;counter_3++){
                                let current_radio = document.createElement("input");
                                current_radio.setAttribute("type","radio");
                                current_radio.setAttribute("value",current_tags[counter_3].value);
                                current_radio.setAttribute("id",choose_labels.id+counter_3.toString());
                                choose_radio.appendChild(current_radio);
                                let current_name = document.createElement("label");
                                current_name.setAttribute("for",current_radio.id);
                                current_name.textContent=current_radio.value.split("_")[1];
                                choose_labels.appendChild(current_name);
                                if (tags!=null){
                                    for (counter_4=0;counter_4<tags.length-1;counter_4++){
                                        if(current_tags[counter_3].value==tags[counter_4]){
                                            current_radio.setAttribute("checked", true);
                                            $(current_name).css({"background-color":"lightcoral", "color":"#fcf2dc"});
                                            current_name.classList.add("bold");
                                            tags_text.innerHTML+=current_radio.value.split("_")[1];
                                            tags_text.innerHTML+=" ";
                                        }
                                    }
                                }
                            }
                            choose_labels.classList.add("hidden");
                            to_div.appendChild(tags_text);
                            to_div.appendChild(choose_radio);
                            to_div.appendChild(choose_labels);
                            to_div.innerHTML+="<br>";
                            let ed = document.createElement("button"); //Create three buttons for edit, delete, and share with the event id for easy identification on click events
                            ed.setAttribute("class","edit");
                            ed.setAttribute("id",stored_event_id[k]);
                            ed.innerHTML = "Edit";
                            let del = document.createElement("button");
                            del.setAttribute("class","delete");
                            del.setAttribute("id",stored_event_id[k]);
                            del.innerHTML = "Delete";
                            let add = document.createElement("button");
                            add.setAttribute("class", "add_user");
                            add.setAttribute("id",stored_event_id[k]);
                            add.innerHTML = "Share";
                            to_div.appendChild(ed);
                            to_div.appendChild(del);
                            to_div.appendChild(add);
                            to_div.innerHTML += "<br>";
                            //Determines whether or now to append the child
                            let wrapper = document.createElement("span");
                            wrapper.setAttribute("id","has_event");
                            wrap(current_element.children[0],wrapper);
                            to_insert = false;
                        }
                    }
                    if (to_div.children.length!=0){
                        current_element.append(to_div);
                    }
                }
                else{
                    if (document.getElementById(a.toString()+b.toString().children!=null)){
                        document.getElementById(a.toString()+b.toString()).remove();
                    }
                //Deleted code 
                }
        })
        .catch(error => console.error('Error:',error));
    }
    function store_edit(id){ //function that is used to store all event information of the most recently accessed event when an user decides to edit the event or share the event 
        event_name = $("p#"+id+".event_name").html();
        event_description = $("p#"+id+".event_description").html();
        event_date = $("p#"+id+".event_date").html();
        event_time = $("p#"+id+".event_time").html();
        event_tags="";
        $(event.target).parent().find(".choose_tags").children().each(function(){
            if($(this).is(":checked")){
                event_tags+=($(this).val()+",");
            }
        });
    }
    //JavaScript
    $(document).ready(function(){
        let temp;
        $("button#register").click(register_button);
        $("button#login").click(login);
        $('#username , #password').keypress(function (e) {      //Log in if user presses enter. Code adapted from https://stackoverflow.com/questions/37859199/login-when-press-enter-in-javascript
            let key = e.which;
            if(key == 13){  // the enter key code
                if ($("div.login_register").children()[2].id=="login"){
                    login();
                }
                else{
                    register();
                }
            }
        });
        $(document).on('click', "#logout_button", logout);
        $("#sharing_user").dialog({ //Dialog dedicated for sharing an event with users
            autoOpen:false,
           // modal:true,
            close:function(){
                $("#sharing_user").dialog("close");
                refreshMonth();
            },
            open:function(){
                //$(document.body).children()[$(document.body).children().length-1].remove();
                $("#sharing_user").dialog("open");
            },
            buttons:{
                "Share!":function(){
                    if ($("#share_users").val()!=null){
                        let to_share = $("#users").val().split(";");
                        for (let counter =0; counter<to_share.length; counter++){
                            let to_check = $.trim(to_share[counter]);
                            fetch("check_user.php",{
                                method:'POST',
                                body:JSON.stringify({user:to_check, token:sess_token})
                            })
                            .then(res=>res.json())
                            .then(function(result){
                                if (result.id!=null){
                                    let event = {event_name:event_name, event_description:event_description, event_due:event_date,tags:event_tags, created_id:user_id,event_time:event_time,shared_id:result.id,token:sess_token};
                                    $.post("share_event.php", //the same as creating a new event except the created_id and shared_id are different
                                        JSON.stringify(event), 
                                        function(data, status){
                                        alert(data);
                                    });
                                }
                                else{
                                    alert(to_share[counter]+" is not a valid username please try again!")
                                }
                            })
                        }
                    }
                    else{
                        alert("Please enter the username of the person you want to share with you!")
                    }
                    $("#sharing_user").dialog("close");
                },
                "Cancel":function(){
                    $(document.body).children()[$(document.body).children().length-2].remove();
                    $("#sharing_user").dialog("close");
                }
            }
        });
        $("#dialog").dialog({ //jQuery dialog dedicated for an user to add an event
            minWidth: 300,
            minHeight: 200,
            autoOpen:false,
            //modal:true,
            open:function(){
                //  $(document.body).css({'opacity': 0.3});
            },
            buttons:[{
                text:"Add this event",
                click:function(){
                    fetch("check_login.php",{method:'POST'})
                    .then(res => res.json())
                    .then(function(result){
                        if (result.message=='true'){
                            user_id = result.id;
                        }
                        else{
                            user_id = null;
                        }
                        php_tags="";
                        $("div#add_event_tags").children().each(function(){
                            if($(this).is(":checked")){
                                php_tags+=($(this).val()+",");
                            }
                        });
                        if ($("#share_users").val()!=""){ //checks if any users are included in the group event field
                            let to_share = $("#share_users").val().split(";");
                            for (let counter =0; counter<to_share.length; counter++){
                                let to_check = $.trim(to_share[counter]);
                                fetch("check_user.php",{
                                    method:'POST',
                                    body:JSON.stringify({user:to_check,token:sess_token})
                                })
                                .then(res=>res.json())
                                .then(function(result){
                                    if (result.id!=null){
                                        let event = {event_name:$("input#event_name").val(), event_description:$("#event_description").val(), event_due:$("#event_due").val(),tags:php_tags, created_id:user_id,event_time:document.getElementById("event_time").value,shared_id:result.id,token:sess_token};
                                        $.post("event.php",
                                            JSON.stringify(event), 
                                            function(data, status){
                                            alert(to_check+" has been succesfully added to this event!");
                                        });
                                    }
                                    else{
                                        alert(to_share[counter]+" is not a valid username please try again!")
                                    }
                                })
                                .catch(error => console.error('Error:',error));
                            }
                        }
                        //The code below creates the event for the user currently using the calendar after everyone that should be in the group event is added to it
                        let event_2 = {event_name:$("input#event_name").val(), event_description:$("#event_description").val(), event_due:$("#event_due").val(),tags:php_tags, created_id:user_id, event_time:$("#event_time").val(), shared_id:user_id, token:sess_token};
                        $.post("event.php",
                            JSON.stringify(event_2), 
                            function(data, status){
                            alert("Data: " + data + "\nStatus: " + status);
                            $("input#event_name").val("") ;
                            $("#event_description").val("");
                            $("#event_due").val("");
                            $("#event_time").val("");
                            $("#share_users").val("");
                            $("div#add_event_tags").children().each(function(){
                                if($(this).is(":checked")){
                                    $("label[for="+$(this).attr("id")+"]").removeClass("bold");
                                    $("label[for="+$(this).attr("id")+"]").css({"background-color":"#fcf2dc", "color":"black"});
                                }
                                $(this).prop("checked",false);
                            });  
                        });
                    })
                    .catch(error => console.error('Error:',error));
                    //Auto close and refresh     
                    $("#dialog").dialog("close");
                    refreshMonth();
                    //Clear input fields 
                    }           
                }]
            });
        $("#add_Event").click(function(){ //generates the radio buttons for the add event dialog
            $("div#add_event_tags.choose_tags").children().each(function(){
                let check_add;
                $(this).checkboxradio();
                let temp_id=$(this).attr("id");
                $("label[for="+temp_id+"]").hover(function(e){
                    check_add=$("#"+temp_id).is(":checked");
                });
                $(this).click(function(e){
                    check_add = !check_add;
                    $(this).prop("checked",check_add);
                    if (check_add){
                        $("label[for="+$(this).attr("id")+"]").addClass("bold");
                        $("label[for="+$(this).attr("id")+"]").css({"background-color":"lightcoral", "color":"#fcf2dc"});
                    }
                    else{
                        $("label[for="+$(this).attr("id")+"]").removeClass("bold");
                        $("label[for="+$(this).attr("id")+"]").css({"background-color":"#fcf2dc", "color":"black"});
                    }

                });
            });
            $("#dialog").dialog("open");
        });
        $(document).on('click', "button.delete", function() {  //When delete is clicked
            let data = {event_id: this.id,current_id:user_id,token:sess_token};
        fetch('delete_event.php', {
            method: "POST",
            body: JSON.stringify(data)
            })
        .then(res => res.json())
        .then(response => console.log('Success:', JSON.stringify(response.event)))
        .catch(error => console.error('Error:',error));
        $(document.body).children()[$(document.body).children().length-1].remove();
        refreshMonth();
        });
        $("#calendar tr").each(function(){
            $("td",this).each(function(){
                let a =$(this);
                let col = a.parent().children().index(a);  //Computing the column and row of a particular date cell, referenced https://stackoverflow.com/questions/788225/table-row-and-column-number-in-jquery
                let row = a.parent().parent().children().index(a.parent());
                a.attr("id",row.toString()+col.toString());
                    a.click(function(e){
                        if (a.find("div.hello").length!=0){
                            a.find("div.hello").dialog(a).dialog({
                                autoOpen:false,
                                //modal:true,
                                close: function(){
                                    $(document.body).children()[$(document.body).children().length-1].remove();
                                    //$(".ui-dialog.ui-corner-all.ui-widget.ui-widget-content.ui-front.ui-dialog-buttons.ui-draggable.ui-resizable")[0].nextElementSibling.remove();
                                    a.find('div.hello').dialog(a).dialog("close");
                                    refreshMonth();
                                }
                            });
                            a.find('div.hello').dialog(event_of_day).dialog("open");
                        } 
                });
            });
        });
        $(document).on('click', "button.edit", function() {    //When edit is clicked
            $("div#"+this.id+".text_tags").addClass("hidden");
            $("div#"+this.id+".choose_tags").children().each(function(){
                $(this).checkboxradio();
                let temp_id=$(this).attr("id");
                let temp_check;
                $("label[for="+temp_id+"]").hover(function(e){
                    temp_check=$("#"+temp_id).is(":checked");
                });
                $(this).click(function(e){
                    temp_check = !temp_check;
                    $(this).prop("checked",temp_check);
                    if (temp_check){
                        $("label[for="+$(this).attr("id")+"]").css({"background-color":"lightcoral", "color":"#fcf2dc"});
                    }
                    else{
                        $("label[for="+$(this).attr("id")+"]").css({"background-color":"#fcf2dc", "color":"black"});
                    }

                });
            });
            $("button.add_user").addClass("hidden");            
            $("div#"+this.id+".choose_names").removeClass("hidden");
            store_edit(this.id);    //Store the original information in case cancel is clicked
            let edit_name = document.createElement("INPUT");
            edit_name.setAttribute("type", "text");
            edit_name.setAttribute("value",$("p#"+this.id+".event_name").html());
            let edit_description = document.createElement("textarea");  //Create description textarea
            edit_description.rows = "5";
            edit_description.cols = "40";
            edit_description.innerHTML+=($("p#"+this.id+".event_description").html());
            let edit_date = document.createElement("INPUT");    //Create date input
            edit_date.setAttribute("id",this.id+"event_date");
            edit_date.setAttribute("type","date");
            edit_date.setAttribute("value",$("p#"+this.id+".event_date").html());
            let line_break = document.createElement("br");            //Creates line break
            $(line_break).insertAfter($("label#"+this.id+".event_time_label"));
            $(edit_date).insertAfter($(line_break));
            let edit_time = document.createElement("INPUT");    //Create time input
            edit_time.setAttribute("id",this.id+"event_time");
            edit_time.setAttribute("type","time");
            edit_time.setAttribute("value",event_time);
            $(edit_time).insertAfter($("#"+this.id+"event_date"));
            // edit_name.setAttribute("value",this.parentNode.;
            $("p#"+this.id+".event_name").html(edit_name);
            $("p#"+this.id+".event_description").html(edit_description);
            $("p#"+this.id+".event_time").remove();
            $("button#"+this.id+".edit").html("Submit");
            $("button#"+this.id+".edit").attr("class","edit_submit");
            $("button#"+this.id+".delete").html("Cancel");
            $("button#"+this.id+".delete").attr("class","edit_cancel");
        });
        $(document).on('click', "button.edit_submit", function(event) {    //When submit is clicked when editing
            $("div#"+this.id+".text_tags").removeClass("hidden");
            $("div#"+this.id+".choose_names").addClass("hidden");
            php_tags="";
            // $(event.target).parent().find(".choose_tags").children().each(function(){
                $("div#"+this.id+".choose_tags").children().each(function(){
                if($(this).is(":checked")){
                    php_tags+=($(this).val()+",");
                }
            });
            let data = {event_id: this.id, event_name: $("p#"+this.id+".event_name")[0].children[0].value, event_description: $("p#"+this.id+".event_description")[0].children[0].value, event_due:$("#"+this.id+"event_date").val(), event_time:$("#"+this.id+"event_time").val(),tags:php_tags,current_id:user_id,token:sess_token};
            fetch('edit_event.php', {
            method: "POST",
            body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(response => console.log('Success:', response))
            .catch(error => console.error('Error:',error));
            refreshMonth();
            $(this).find('div.hello').dialog(get_events).dialog("close");
            $(document.body).children()[$(document.body).children().length-1].remove();
        });
        $(document).on('click', "button.edit_cancel", function(){    //When cancel is clicked when editing
            $("div#"+this.id+".text_tags").removeClass("hidden");
            $("button.add_user").removeClass("hidden");   
            $("div#"+this.id+".choose_names").addClass("hidden");
            $("p#"+this.id+".event_name").html(event_name);             //Revert back to original
            $("p#"+this.id+".event_description").html(event_description);
            let original_time = document.createElement("p");            //Gets event_time back
            original_time.setAttribute("id",this.id);
            original_time.setAttribute("class","event_time");
            let line_break = document.createElement("br");            //Creates line break
            $(original_time).insertAfter($("label#"+this.id+".event_time_label"));
            $(line_break).insertAfter($(original_time));
            $(original_time).html(event_time);
            $("button#"+this.id+".edit_submit").html("Edit");           //Revert to edit button
            $("button#"+this.id+".edit_submit").attr("class","edit");  
            $("button#"+this.id+".edit_cancel").html("Delete");         //Revert to delete button
            $("button#"+this.id+".edit_cancel").attr("class","delete");    
            store_edit(this.id);   
            $("#"+this.id+"event_date").remove();        //Gets rid of date 
            $("input#"+this.id+"event_time").remove();        //Gets rid of time
            $('p#'+this.id+".event_time").next('br').remove();  //Removes extra line breaks
            $('p#'+this.id+".event_time").next('br').remove();
        });
        $(document).on('click',"button.add_user",function(){
            store_edit(this.id);
            $(document.body).children()[$(document.body).children().length-1].remove();
            // refreshMonth();
            $("#sharing_user").dialog("open");
        }); 
    });
    </script>
</head>
<body>
<div id = "sharing_user" title = "Share this event with someone!">
    <label id = "sharing">Enter usernames here, each separated by a ;</label>
    <input type = "text" id = "users">
</div>
<div class ="login_register">
        <input type = "text" id = "username" placeholder= "Enter Username Here">
        <input type = "password" id = "password" placeholder = "Enter Password Here">
        <button id = "login">Login</button>
        <button id = "register">Register</button>
    </div>
    <div class="logout">
        <input id = "logout_button" type = 'submit' value ='Log Out'>
        <br><br>
    </div> 
    <div class = "welcome">
        <h2><span class = 'title'>Welcome <?php if (isset($_SESSION['username'])){
                echo htmlentities($_SESSION['username']);
            } else {
                echo htmlentities('');
            }?>!</span></h2>
    </div>
    <?php
        if (!isSet($_SESSION["user_id"])){ ?>
<script>
                $("div.login_register").css({display:"inline"});
                $("#add_Event").css({display:"inline"});
                $("div.tag_names").css({display:"block"});
            </script>
                <?php
        }
        else{
            ?> 
            <script>
            $("div.logout").css({display:"inline"});
            $("div.welcome").css({display:"inline"});
            $("#add_Event").css({display:"none"});
            $("div.tag_names").css({display:"none"});
            </script>
        <?php
        }
    ?>
    <h2 id = "month_year"></h2>
    <div class = "navigation">
        <button id = "prev">previous</button>
        <button id = "tod">today</button>
        <button id = "next">next</button>
        <button id = "add_Event"> Add Event</button>
    </div>
    <!-- <input type = "text" id = "dialog" title = "hello" placeholder = "event"> -->
    <div id="dialog" title = "Create a new event!">
        <label>Event Title:</label> <br>
        <input type = "text" id = "event_name"> <br>
        <label>Event Description:</label> <br>
        <textarea id = "event_description" rows = "5" cols = "40"></textarea> <br>
        <label>Due Date:</label>
        <input type = "date" id = "event_due"> <br>
        <label>Event Time:</label>
        <input type = "time" id = "event_time">
        <div class = "choose_tags" id = "add_event_tags">
            <input type ="radio" value ="tag_work" id ="choose_work">
            <input type ="radio" value ="tag_school" id = "choose_school">
            <input type ="radio" value ="tag_ent" id = "choose_ent">
        </div>
        <div class = "choose_names" id = "add_event_names">
            <label>Event Tags:</label>
            <label for = "choose_work">Work</label>
            <label for = "choose_school">School</label>
            <label for = "choose_ent">Entertainment</label>
        </div>
        <label>Would you like to share with other users?</label>
        <br><label>(separate username by ';')</label>
        <input type = "text" id = "share_users">
    </div>
    <table class = "calendar" id = "calendar">
        <thead>
            <tr>
                <th>Sun</th>
                <th>Mon</th>
                <th>Tue</th>
                <th>Wed</th>
                <th>Thu</th>
                <th>Fri</th>
                <th>Sat</th>
            </tr>
        </thead>
        <tr id = "0">
            <td class = "0"></td>
            <td class = "1"></td>
            <td class = "2"></td>
            <td class = "3"></td>
            <td class = "4"></td>
            <td class = "5"></td>
            <td class = "6"></td>
        </tr>
        <tr id = "1">
            <td class = "0"></td>
            <td class = "1"></td>
            <td class = "2"></td>
            <td class = "3"></td>
            <td class = "4"></td>
            <td class = "5"></td>
            <td class = "6"></td>
        </tr>
        <tr id = "2">
            <td class = "0"></td>
            <td class = "1"></td>
            <td class = "2"></td>
            <td class = "3"></td>
            <td class = "4"></td>
            <td class = "5"></td>
            <td class = "6"></td>
        </tr>
        <tr id = "3">
            <td class = "0"></td>
            <td class = "1"></td>
            <td class = "2"></td>
            <td class = "3"></td>
            <td class = "4"></td>
            <td class = "5"></td>
            <td class = "6"></td>
        </tr>
        <tr id = "4">
            <td class = "0"></td>
            <td class = "1"></td>
            <td class = "2"></td>
            <td class = "3"></td>
            <td class = "4"></td>
            <td class = "5"></td>
            <td class = "6"></td>
        </tr>
      </table>
    <div class = "tags">
      <input type ="radio" value ="work" id ="tag_work">
      <input type ="radio" value ="school" id = "tag_school">
      <input type ="radio" value ="entertainment" id = "tag_ent">
    </div>
    <div class = "tag_names">
    <label>Filter by tags:</label>    
        <label for = "tag_work">Work</label>
        <label for = "tag_school">School</label>
        <label for = "tag_ent">Entertainment</label>
    </div>
    <script>
        let check = true;
        $(document).ready(function(){
            $("div.tags").children().each(function(){ //jQuery script for hovering checking and unchecking radio buttons that are supposed to be the tags on the bottom of the page which serves as a filter sytem
                $(this).checkboxradio();
                let temp_id=$(this).attr("id");
                $("label[for="+temp_id+"]").hover(function(){
                    check=$("#"+temp_id).is(":checked");
                });
                $(this).click(function(){
                    check = !check;
                    $(this).prop("checked",check);
                    if (check){
                        $("label[for="+$(this).attr("id")+"]").css({"background-color":"lightcoral", "color":"#fcf2dc"});
                    }
                    else{
                        $("label[for="+$(this).attr("id")+"]").css({"background-color":"#fcf2dc", "color":"black"});
                    }
                    checked_tags="";
                    $("div.tags").children().each(function(){
                        if ($(this).is(":checked")){
                            checked_tags+=($(this).attr("id")+",");
                    }
                });
                if (checked_tags!=""){
                    checked_tags = checked_tags.split(",");
                }
                refreshMonth();
                });
            });
            $("div.choose_tags").children().each(function(){
                $(this).checkboxradio();
                let temp_id=$(this).attr("id");
                $("label[for="+temp_id+"]").hover(function(e){
                    check=$("#"+temp_id).is(":checked");
                });
                $(this).click(function(e){
                    check = !check;
                    $(this).prop("checked",check);
                    if (check){
                        $("label[for="+$(this).attr("id")+"]").css({"background-color":"lightcoral", "color":"#fcf2dc"});
                    }
                    else{
                        $("label[for="+$(this).attr("id")+"]").css({"background-color":"#fcf2dc", "color":"black"});
                    }

                });
            });
        //     $("div.choose_tags").children().each(function(){
        //         let temp_id=$(this).attr("id");
        //         $("label[for="+$(this).attr("id")+"]").hover(function(){
        //             check=$("#"+temp_id).is(":checked");
        //         });
        //         $(this).click(function(){
        //         check = !check;
        //         $(this).prop("checked",check);
        //         if (check){
        //             $("label[for="+$(this).attr("id")+"]").addClass("bold");
        //         }
        //         else{
        //             $("label[for="+$(this).attr("id")+"]").removeClass("bold");
        //         }
        //         });
        //     });
        //     $("div.choose_names").children().each(function(key,value){
        //         value.click(function(){
        //         });
        //     });
         });
        document.addEventListener("DOMContentLoaded", today, false);
        document.getElementById("prev").addEventListener("click",function(){
            if (m>0){
                m--;
            } else{
                y--;
                m=11;
            } refreshMonth()
        }, false);
        document.getElementById("next").addEventListener("click",function(){
            if (m<11){
                m++;
            } else{
                y++;
                m=0;
            } refreshMonth()
        }, false);
        document.getElementById("tod").addEventListener("click", today, false);
    </script>
</body>
</html>