{if $printTemplate}
{* this is the xmlform template *}
<form id="{$form->id}" name="{$form->name}" action="{$form->action}" class="{$form->className}" method="post" encType="multipart/form-data" style="margin:0px;" onsubmit="{$form->onsubmit}">
  <div class="borderForm" style="width:{$form->width}; padding-left:0; padding-right:0; border-width:{$form->border};">
		<div class="boxTop"><div class="a">&nbsp;</div><div class="b">&nbsp;</div><div class="c">&nbsp;</div></div>
		<div class="content" style="height:{$form->height};" >
	  <table width="99%">
      <tr>
        <td valign='top'>
           <table cellspacing="0" cellpadding="0" border="0" width="100%">
          {foreach from=$form->fields item=field}
            {if ($field->type==='title')}
             <tr>
              <td class='FormTitle' colspan="2" align="{$field->align}">{$field->field}</td>
             </tr>
            {elseif ($field->type==='subtitle')}
            <tr>
              <td class='FormSubTitle' colspan="2" align="{$field->align}" id="form[{$field->name}]">
                <span style="float:left;">{$field->field}</span>
                {if (isset($field->showHide) && $field->showHide)}
                <a style="float:right;" href="#" onclick="contractExpandSubtitle(this);return false;">Hide</a>
                {/if}
                </td>
             </tr>
            {elseif ($field->type==='button') || ($field->type==='submit') || ($field->type==='reset')}
             <tr>
              <td class='FormButton' colspan="2" align="{$field->align}">{$field->field}</td>
             </tr>
            {elseif ($field->type==='grid')}
             <tr>
              <td colspan="2">{$field->field}</td>
             </tr>
            {elseif ($field->type==='checkbox') && ($field->labelOnRight)}
             <tr>
              <td class='FormLabel' width="{$form->labelWidth}"></td>
              <td>{$field->field}</td>
             </tr>
            {elseif ($field->type==='phpvariable')}
            {elseif ($field->type==='private')}
            {elseif ($field->type==='javascript')}
            {elseif ($field->type==='hidden')}
            <tr style="display: none">
              <td colspan="2">{$field->field}</td>
             </tr>
            {elseif ($field->type==='')}
            {elseif ($field->withoutLabel)}
            <tr>
              <td colspan="2" class="withoutLabel">{$field->field}</td>
             </tr>
            {elseif (isset($field->withoutValue) && $field->withoutValue)}
            <tr>
              <td class='FormLabel' colspan="2"><div align="{$field->align}">{$field->label}</div></td>
             </tr>
            {else}
              <tr>
              <td class='FormLabel' width="{$form->labelWidth}">{if (isset($field->required)&&$field->required&&$field->mode==='edit')}<font color="red">*  </font>{/if}{$field->label}</td>
              <td class='FormFieldContent' {* width="{math equation="parseFloat(x)-parseFloat(y)" x=$form->width y=$form->labelWidth}" *}>{$field->field}</td>
              </tr>
            {/if}
          {/foreach}
           </table>
        </td>
      </tr>
    </table>
    {if $hasRequiredFields}<div class="FormRequiredTextMessage"><font color="red">*  </font>{php}echo (G::LoadTranslation('ID_REQUIRED_FIELD'));{/php}</div>{/if}
		</div>
		<div class="boxBottom"><div class="a">&nbsp;</div><div class="b">&nbsp;</div><div class="c">&nbsp;</div></div>
  </div>
{foreach from=$form->fields item=field}
  {if ($field->type==='javascript')}
  <script type="text/javascript">
    {$field->field}
  </script>
  {/if}
{/foreach}
</form>
{/if}
{if $printJSFile}
{* TODO: include file='xmlformScript.html' *}
var form_{$form->id};
var object_{$form->name};
var i;
function loadForm_{$form->id}(ajaxServer)
{literal}{{/literal}
if (typeof(G_Form)==='undefined') return alert('form.js was not loaded');
  form_{$form->id}=new G_Form(document.getElementById('{$form->id}'),'{$form->id}');
  object_{$form->name} = form_{$form->id};
  var myForm=form_{$form->id};
  if (myForm.aElements===undefined) alert("{$form->name}");
  myForm.ajaxServer=ajaxServer;
  
  {if isset($form->ajaxSubmit) && ($form->ajaxSubmit)}
  {literal}
    var sub = new leimnud.module.app.submit({
    form    : myForm.element,{/literal}
    inProgress: {$form->in_progress},
    callback: {$form->callback}
    {literal}
      });
    sub.sendObj = false;
  {/literal}
  {/if}
  {foreach from=$form->fields item=field key=name}
    i = myForm.aElements.length;
    {if (($field->type==='dropdown') || $field->type==='listbox')}
      myForm.aElements[i] = new G_DropDown(myForm, myForm.element.elements['form[{$name}]'],'{$name}');
      myForm.aElements[i].setAttributes({$field->getAttributes()});
      {$field->attachEvents("myForm.aElements[i].element")}
    {elseif ($field->type==='text')}
      myForm.aElements[i] = new G_Text(myForm, myForm.element.elements['form[{$name}]'],'{$name}');
      myForm.aElements[i].setAttributes({$field->getAttributes()});
      {$field->attachEvents("myForm.aElements[i].element")}
    {elseif ($field->type==='percentage')}
      myForm.aElements[i] = new G_Percentage(myForm, myForm.element.elements['form[{$name}]'],'{$name}');
      myForm.aElements[i].setAttributes({$field->getAttributes()});
      {$field->attachEvents("myForm.aElements[i].element")}
    {elseif ($field->type==='currency')}
      myForm.aElements[i] = new G_Currency(myForm, myForm.element.elements['form[{$name}]'],'{$name}');
      myForm.aElements[i].setAttributes({$field->getAttributes()});
      {$field->attachEvents("myForm.aElements[i].element")}
    {elseif ($field->type==='textarea')}
      myForm.aElements[i] = new G_TextArea(myForm, myForm.element.elements['form[{$name}]'],'{$name}');
      myForm.aElements[i].setAttributes({$field->getAttributes()});
      {$field->attachEvents("myForm.aElements[i].element")}
    {elseif ($field->type==='date')}
      myForm.aElements[i] = new G_Date(myForm, myForm.element.elements['form[{$name}]'],'{$name}');
      myForm.aElements[i].setAttributes({$field->getAttributes()});
      {$field->attachEvents("myForm.aElements[i].element")}
    {elseif ($field->type==='hidden')}
      myForm.aElements[i] = new G_Text(myForm, myForm.element.elements['form[{$name}]'],'{$name}');
      myForm.aElements[i].setAttributes({$field->getAttributes()});
      {$field->attachEvents("myForm.aElements[i].element")}
    {elseif ($field->type==='grid')}
      myForm.aElements[i] = new G_Grid(myForm, '{$name}');
      grid_{$field->id}(myForm.aElements[i]);
      {$field->attachEvents("myForm.aElements[i].element")}
    {else}
      var element = getField("{$name}");
      {$field->attachEvents("element")}
    {/if}
  {/foreach}
  {foreach from=$form->fields item=field key=name}
    {if isset($field->dependentFields) && ($field->dependentFields!='')}
      {if ($field->type==='dropdown')}
          myForm.getElementByName('{$name}').setDependentFields('{$field->dependentFields}');
      {elseif ($field->type==='text')}
          myForm.getElementByName('{$name}').setDependentFields('{$field->dependentFields}');
      {elseif ($field->type==='percentage')}
          myForm.getElementByName('{$name}').setDependentFields('{$field->dependentFields}');
      {elseif ($field->type==='currency')}
          myForm.getElementByName('{$name}').setDependentFields('{$field->dependentFields}');
      {elseif ($field->type==='date')}
          myForm.getElementByName('{$name}').setDependentFields('{$field->dependentFields}');
      {/if}
    {/if}
  {/foreach}
{literal}}{/literal}
{/if}
{if $printJavaScript}
leimnud.event.add(window,'load',function(){literal}{{/literal}loadForm_{$form->id}('{$form->ajaxServer}');{literal}}{/literal});
{/if}
