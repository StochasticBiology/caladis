// this code contains several function used in the modification and behaviour of the Caladis home screen
  // Copyright (C) 2014 Systems & Signals Research Group, Imperial College London
  // Released as free software under GNU GPL 3.0 http://www.gnu.org/licenses/ ; see readme.txt
  // Please cite the accompanying paper [see readme.txt] if using this code or this tool in a publication. 

var loaded = false;

/** get content for lists in Bionumbers browser **/
/** this is called by some functions below and wraps a call to listget.php with different arguments dictating which elements of the list we are interested in **/
/** cheap hack to prevent Ajax search for organism list interfering with (long) search for browsing list: use same call and single recursion for list0 (long browse list) and list1 (organism list) */
function getContent(object, nn, n1, n2, n3)
{
  var responsestr;
  var xmlHttp;

  if (window.XMLHttpRequest)
  {  // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp=new XMLHttpRequest();
  }
  else
  {  // code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlhttp.onreadystatechange=function()
  {
    if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
      responsestr=xmlhttp.responseText;
      object.innerHTML = responsestr;
        if(object.id == 'list0')
        {
          getContent(document.getElementById('list1'), 0, 0, 0, 0);
        }
        if(object.id == 'list1')
        {
          loaded = true;
          document.getElementById('loading').innerHTML = '';
        }
    }
  }

  var submitStr = "lib/listget.php?nn="+nn+"&n1="+n1+"&n2="+n2+"&n3="+n3;

  xmlhttp.open("GET",submitStr);
  xmlhttp.send();
}

/** retrieve results for a search in the Bionumbers Browser **/
function getBionumSearchReal()
{
  var searchstr = document.getElementById('bionumtextsearch').value;
  var responsestr;
  var xmlHttp;
  var object = document.getElementById('listsearch');

  if (window.XMLHttpRequest)
  {  // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp=new XMLHttpRequest();
  }
  else
  {  // code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlhttp.onreadystatechange=function()
  {
    if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
      responsestr=xmlhttp.responseText;
      object.innerHTML = responsestr;
    }
  }

  var submitStr = "lib/listsearch.php?s="+searchstr;

  xmlhttp.open("GET",submitStr);
  xmlhttp.send();
}

/* populate Bionumbers Browser list2 (Bionumber type) */
function getList2(num)
{
  document.getElementById('output2').innerHTML = '';
  if(num != 0)
  {
    document.getElementById('list2').innerHTML = '<select width = "180" style = "width:180px"><option name = "placehold">Reading from database...</option></select>';
    getContent(document.getElementById('list2'), 1, num, 0, 0);
    document.getElementById('list3').innerHTML = '<select width = "180" style = "width:180px"><option name = "placehold">Please select a type of Bionumber...</option></select>';
  }
  else 
  {
    document.getElementById('list2').innerHTML = document.getElementById('list3').innerHTML = '<select width = "180" style = "width:180px"><option name = "placehold">Please select an organism...</option></select>';
  }
}

/* populate Bionumbers Browser list3 (individual Bionumbers) */
function getList3(num1, num2)
{
  document.getElementById('output2').innerHTML = '';
  if(num2 != 0)
  {
    document.getElementById('list3').innerHTML = '<select width = "180" style = "width:180px"><option name = "placehold">Reading from database...</option></select>';
    getContent(document.getElementById('list3'), 2, num1, num2, 0);  
  }
  else
  {
    document.getElementById('list3').innerHTML = '<select width = "180" style = "width:180px"><option name = "placehold">Please select a type of Bionumber...</option></select>';
  }
}

/* display div containing selected bionumber */
/* and link to send back to expression box   */
function displayBionumber(listnum, num)
{
  if(listnum == 1) object = document.getElementById('output1');
  if(listnum == 2) object = document.getElementById('output2');
  if(listnum == 3) object = document.getElementById('output3');

  if(num == 0) object.innerHTML = '';
  else object.innerHTML = 'Bionumber #'+num+' selected: <a href = "javascript:void(0);" onclick = "addBionum('+num+')">add to expression</a>.';
}

/* adds a selected bionumber to expression box */
/* pokes input.js to figure out its details    */
function addBionum(num)
{
  document.getElementById('qi-input').value += '#'+num;
  vFetch( $('#qi-input').val());
}

//**********
//** processCross
//** makes Bionumbers browser (in)visible
//** also wipes loaded content: as long dropdown lists slow things down
//** also positions the browser into a suitable position on screen -- so is called upon window resizing
//************
function processCross(num) {
  if(num == 1)
  {
    document.getElementById('wrapper').style.display = 'block';
    var rect1 = document.getElementById('iaincontainer').getBoundingClientRect();
    var rect2 = document.getElementById('link-list-iainmod').getBoundingClientRect();
    var x, y;

    x = rect1.left;
    y = rect2.bottom;
    document.getElementById('wrapper').style.left = x+'px';
    document.getElementById('wrapper').style.top = y+'px';
    if(loaded == false)
    {
      getContent(document.getElementById('list0'), -1, 0, 0, 0);
    }
  }
  else
  {
    document.getElementById('wrapper').style.display = 'none';
    document.getElementById('loading').innerHTML = '<center>Loading...<br><img src = "media/img/loader/bar.gif"></center><br>';
    document.getElementById('list0').innerHTML = '<select width="180" style="width:180px"><option value = "0" name="placehold">Reading from database...</option></select>';
    document.getElementById('output1').innerHTML = '';
    document.getElementById('list1').innerHTML = '<select width="180" style="width:180px"><option value = "0" name="placehold">Reading from database...</option></select>';
    document.getElementById('list2').innerHTML = '<select width="180" style="width:180px"><option value = "0" name="placehold">Please select an organism...</option></select>';
    document.getElementById('list3').innerHTML = '<select width="180" style="width:180px"><option value = "0" name="placehold">Please select an organism...</option></select>';
    document.getElementById('output2').innerHTML = '';
      document.getElementById('bionumtextsearch').value = '';
    document.getElementById('listsearch').innerHTML = '<select width="180" style="width:180px"><option value = "0" name="placehold">Please enter a search term...</option></select>';

    document.getElementById('output3').innerHTML = '';
    loaded = false;
  }
}

//***********
//** changecss
//** rewrite the css description for a given class
//** changes 'element' to 'value' in class 'theClass'
//** use this to change the "Search for this Bionumber" class (in)visible: so that all future boxes built in JS will share the property
//************
function changecss(theClass,element,value) {
  var cssRules;

  for (var sheetRef = 0; sheetRef < document.styleSheets.length; sheetRef++)
  {
    try
    { // if the rule doesn't exist
      document.styleSheets[sheetRef].insertRule(theClass+' { '+element+': '+value+'; }',document.styleSheets[sheetRef][cssRules].length);
    } catch(err)
    {
      try
      { // if the rule doesn't exist
        document.styleSheets[sheetRef].addRule(theClass,element+': '+value+';');
      }
      catch(err)
      {
        try{
          if (document.styleSheets[sheetRef]['rules'] || document.styleSheets[sheetRef]['cssRules']) 
          {
            if (document.styleSheets[sheetRef]['rules'])
            { // browser1
              cssRules = 'rules';
            } 
            else if (document.styleSheets[sheetRef]['cssRules']) 
            { // browser2
              cssRules = 'cssRules';
            }
            for (var ruleRef = 0; ruleRef < document.styleSheets[sheetRef][cssRules].length; ruleRef++) 
            { // loop through rules until we find the one we want
              if (document.styleSheets[sheetRef][cssRules][ruleRef].selectorText == theClass) 
              {
                if(document.styleSheets[sheetRef][cssRules][ruleRef].style[element])
                {
                  document.styleSheets[sheetRef][cssRules][ruleRef].style[element] = value;
   	          break;
	        }
              }
            }
          } 
          else 
          {
            for (var ruleRef = 0; ruleRef < document.styleSheets.cssRules.length; ruleRef++) 
             {
               if (document.styleSheets[sheetRef].cssRules[ruleRef].selectorText == theClass) 
               {
                 if(document.styleSheets[sheetRef].cssRules[ruleRef].style[element])
                 {
                   document.styleSheets[sheetRef].cssRules[ruleRef].style[element] = value;
   	           break;
	         }
               }
             }
           }
         } catch (err){}
       }
     }
   }
}

// Toggle the input buttons on the home screen (in)visible
function togglebuttons(num)
{
  if(document.getElementById('buttonsbit').style.display == 'block')
  {
    document.getElementById('buttonsbit').style.display = 'none';
  }
  else
    {
    document.getElementById('buttonsbit').style.display = 'block';
    }
}

// Wipe the content of the input expression box
function clearInput(){
    $('#qi-input').val("");
    vFetch( $('#qi-input').val());
    $('.help-welcome').remove(); $('.vi').remove(); $('.help-example').remove(); 
}
