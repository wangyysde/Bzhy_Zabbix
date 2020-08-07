<script type="text/x-jquery-tmpl" id="hostInterfaceRow">
    <tr class="interfaceRow" id="hostInterfaceRow_first_#{iface.interfaceid}"  data-interfaceid="#{iface.interfaceid}">
        <td class="interface-drag-control <?= ZBX_STYLE_TD_DRAG_ICON ?>">
            <div class="<?= ZBX_STYLE_DRAG_ICON ?>"></div>
            <input type="hidden" name="interfaces[#{iface.interfaceid}][items]" value="#{iface.items}" />
            <input type="hidden" name="interfaces[#{iface.interfaceid}][locked]" value="#{iface.locked}" />
        </td>
        <td >
            <input type="hidden" name="interfaces[#{iface.interfaceid}][isNew]" value="#{iface.isNew}">
            <input type="hidden" name="interfaces[#{iface.interfaceid}][interfaceid]" value="#{iface.interfaceid}">
            <input type="hidden" id="interface_type_#{iface.interfaceid}" name="interfaces[#{iface.interfaceid}][type]" value="#{iface.type}">
            <input name="interfaces[#{iface.interfaceid}][name]" type="text"  maxlength="64" value="#{iface.name}"> 
        </td>
        <td>
            <input name="interfaces[#{iface.interfaceid}][ip]" type="text"  maxlength="64" value="#{iface.ip}"> 
        </td>
        <td>
            <input name="interfaces[#{iface.interfaceid}][mask]" type="text"  maxlength="64" value="#{iface.mask}">
        </td>
        <td>
            <input name="interfaces[#{iface.interfaceid}][dns]" type="text"  maxlength="64" value="#{iface.dns}">
        </td>
        <?= (new CCol(
            (new CRadioButtonList('interfaces[#{iface.interfaceid}][useip]', null))
				->addValue(_('IP'), INTERFACE_USE_IP, 'interfaces[#{iface.interfaceid}][useip]['.INTERFACE_USE_IP.']')
				->addValue(_('DNS'), INTERFACE_USE_DNS,
					'interfaces[#{iface.interfaceid}][useip]['.INTERFACE_USE_DNS.']'
				)
				->setModern(true)
            ))->toString()
        ?>
    </tr>
    <tr class="interfaceRow" id="hostInterfaceRow_second_#{iface.interfaceid}" data-interfaceid="#{iface.interfaceid}">
        <td colspan='2'> <?=_('Type')?>
            <input type="radio" id="interfaces[#{iface.interfaceid}][kind]" name="interfaces[#{iface.interfaceid}][type]" value="1" #{attrs.entity}><?=_("Entity") ?>
            <input type="radio" id="interfaces[#{iface.interfaceid}][kind]" name="interfaces[#{iface.interfaceid}][type]" value="0" #{attrs.dummy}><?=_("Dummy") ?>
        </td>
        <td><?=_('Enable:') ?>
            <input type="radio" id="interfaces[#{iface.interfaceid}][enable][agent]" name="enable[<?=INTERFACE_TYPE_AGENT ?>]" value="#{iface.interfaceid}"  #{attrs.enable<?=INTERFACE_TYPE_AGENT ?>} onClick='Javascript:chg_port_display(#{iface.interfaceid},<?=INTERFACE_TYPE_AGENT ?>);'><?=_('Agent') ?>
            <span id="interfaces[#{iface.interfaceid}][<?=INTERFACE_TYPE_AGENT ?>][port]" style="display:#{attrs.portdisplay<?=INTERFACE_TYPE_AGENT ?>};"   >
                <input type="text" id="interfaces[#{iface.interfaceid}][port][agent]" name="port[#{iface.interfaceid}][<?=INTERFACE_TYPE_AGENT ?>]" value="#{attrs.portnum<?=INTERFACE_TYPE_AGENT ?>}" style="width: 40px">
             </span>
        </td>
        <td>
            <input type="radio" id="interfaces[#{iface.interfaceid}][enable][snmp]" name="enable[<?=INTERFACE_TYPE_SNMP ?>]"  value="#{iface.interfaceid}" #{attrs.enable<?=INTERFACE_TYPE_SNMP ?>} onClick='Javascript:chg_port_display(#{iface.interfaceid},<?=INTERFACE_TYPE_SNMP ?>);'><?=_('SNMP') ?> 
            <span id="interfaces[#{iface.interfaceid}][<?=INTERFACE_TYPE_SNMP ?>][port]"  style="display:#{attrs.portdisplay<?=INTERFACE_TYPE_SNMP ?>};">
                <input type="text" id="interfaces[#{iface.interfaceid}][port][snmp]" name="port[#{iface.interfaceid}][<?=INTERFACE_TYPE_SNMP ?>]" value="#{attrs.portnum<?=INTERFACE_TYPE_SNMP ?>}" style="width: 40px">
                <input type="checkbox" id="interfaces[#{iface.interfaceid}][bulk]" name="interfaces[#{iface.interfaceid}][bulk]" value="1"  #{attrs.bulk} >
                <label for="interfaces[#{iface.interfaceid}][bulk]"><?= _('bulk') ?></label>
            </span>
        </td>
        <td>
            <input type="radio" id="interfaces[#{iface.interfaceid}][enable][jmx]" name="enable[<?=INTERFACE_TYPE_JMX ?>]"  value="#{iface.interfaceid}" #{attrs.enable<?=INTERFACE_TYPE_JMX ?>} onClick='Javascript:chg_port_display(#{iface.interfaceid},<?=INTERFACE_TYPE_JMX ?>);'><?=_('JMX') ?>
            <span id="interfaces[#{iface.interfaceid}][<?=INTERFACE_TYPE_JMX ?>][port]"  style="display:#{attrs.portdisplay<?=INTERFACE_TYPE_JMX ?>};">
                <input type="text" id="interfaces[#{iface.interfaceid}][port][jmx]" name="port[#{iface.interfaceid}][<?=INTERFACE_TYPE_JMX ?>]" value="#{attrs.portnum<?=INTERFACE_TYPE_JMX ?>}" style="width: 40px">
             </span>
        </td>
        <td>
            <input type="radio" id="interfaces[#{iface.interfaceid}][enable][ipmi]" name="enable[<?=INTERFACE_TYPE_IPMI ?>]"  value="#{iface.interfaceid}" #{attrs.enable<?=INTERFACE_TYPE_IPMI ?>} onClick='Javascript:chg_port_display(#{iface.interfaceid},<?=INTERFACE_TYPE_IPMI ?>);'><?=_('IPMI') ?>    
             <span id="interfaces[#{iface.interfaceid}][<?=INTERFACE_TYPE_IPMI ?>][port]"  style="display:#{attrs.portdisplay<?=INTERFACE_TYPE_IPMI ?>};">
                <input type="text" id="interfaces[#{iface.interfaceid}][port][ipmi]" name="port[#{iface.interfaceid}][<?=INTERFACE_TYPE_IPMI ?>]" value="#{attrs.portnum<?=INTERFACE_TYPE_IPMI ?>}" style="width: 40px">
             </span>
            <button class="<?= ZBX_STYLE_BTN_LINK ?> remove" type="button" id="removeInterface_#{iface.interfaceid}" data-interfaceid="#{iface.interfaceid}" #{attrs.disabled}><?= _('Remove') ?></button> 
        </td>
    </tr>
</script>


<script type="text/javascript">
    var maxInterfaceId = 0;
            
    function chg_port_display(interfaceid,kind){
        for(var i= 0;i<= maxInterfaceId;i++){
            obj_port = document.getElementById("interfaces[" + i + "][" + kind + "][port]");
            if(obj_port){
                if(i == interfaceid){
                    obj_port.style.display = "";
                }
                else{
                    obj_port.style.display = "none";
                }
            }
        }
        
        /*
        obj_interface = document.getElementById("interfaces[" + interfaceid + "][enable][" + kind + "]");
        obj_port = document.getElementById("interfaces[" + interfaceid + "][" + kind + "][port]");
        obj_port.style.display = "";
        if(kind === <?=INTERFACE_TYPE_AGENT ?>){
            if(agent_port_id !== 0 && agent_port_id !== interfaceid){
                obj_port = document.getElementById("interfaces[" + agent_port_id + "][" + kind + "][port]");
                obj_port.style.display = "none";
            }
            agent_port_id = interfaceid;
        }
        
        if(kind === <?=INTERFACE_TYPE_SNMP ?>){
            if(snmp_port_id !== 0 && snmp_port_id !== interfaceid){
                obj_port = document.getElementById("interfaces[" + snmp_port_id + "][" + kind + "][port]");
                obj_port.style.display = "none";
            }
            snmp_port_id = interfaceid;
        }
        
        if(kind === <?=INTERFACE_TYPE_JMX ?>){
            if(jmx_port_id !== 0 && jmx_port_id !== interfaceid){
                obj_port = document.getElementById("interfaces[" + jmx_port_id + "][" + kind + "][port]");
                obj_port.style.display = "none";
            }
            jmx_port_id = interfaceid;
        }
        
        if(kind === <?=INTERFACE_TYPE_IPMI ?>){
            if(ipmi_port_id !== 0 && ipmi_port_id !== interfaceid){
                obj_port = document.getElementById("interfaces[" + ipmi_port_id + "][" + kind + "][port]");
                obj_port.style.display = "none";
            }
            ipmi_port_id = interfaceid;
        }
        */
    }       
    
        
	var hostInterfacesManager = (function() {
		'use strict';

		var rowTemplate = new Template(jQuery('#hostInterfaceRow').html()),
			
        allHostInterfaces = {};

		function renderHostInterfaceRow(hostInterface) {
			var domAttrs = getDomElementsAttrsForInterface(hostInterface),
				domId = getDomIdForRowInsert('agent'),
				domRow;

			jQuery(domId).before(rowTemplate.evaluate({iface: hostInterface, attrs: domAttrs}));

			domRow = jQuery('#hostInterfaceRow_second_' + hostInterface.interfaceid);

			jQuery('#interfaces_' + hostInterface.interfaceid + '_useip_' + hostInterface.useip).prop('checked', true);

			if (hostInterface.locked > 0) {
				addNotDraggableIcon(domRow);
			}
			else {
				addDraggableIcon(domRow);
			}
		}

		function addDraggableIcon(domElement) {
			domElement.draggable({
				handle: 'div.<?= ZBX_STYLE_DRAG_ICON ?>',
				revert: 'invalid',
				start: function(event, ui) {
					jQuery(this).css({'z-index': '1000'})
				},
				stop: function(event, ui) {
					var hostInterfaceId = jQuery(this).data('interfaceid');
				//	resetMainInterfaces();
					resetUseipInterface(hostInterfaceId)

					jQuery(this).css({'z-index': ''})
				}
			});
		}

		function addNotDraggableIcon(domElement) {
			jQuery('td.<?= ZBX_STYLE_TD_DRAG_ICON ?> div.<?= ZBX_STYLE_DRAG_ICON ?>', domElement)
				.addClass('<?= ZBX_STYLE_DISABLED ?>')
				.hover(
					function (event) {
						hintBox.showHint(event, this,
							<?= CJs::encodeJson(_('Interface is used by items that require this type of the interface.')) ?>
						);
					},
					function (event) {
						hintBox.hideHint(event, this);
					}
				);
		}

		function getDomElementsAttrsForInterface(hostInterface) {
			var attrs = {
				disabled: ''
			}; 
            
            if(hostInterface.type == 1){
                attrs.entity = 'checked="checked"';
                attrs.dummy = '';
            }
            else{
                attrs.entity = '';
                attrs.dummy = 'checked="checked"';
            }
            
            attrs.enable<?=INTERFACE_TYPE_AGENT ?> = '';
            attrs.portdisplay<?=INTERFACE_TYPE_AGENT ?> = 'none';
            attrs.portnum<?=INTERFACE_TYPE_AGENT ?> = <?=BZHY_AGENT_DEFAULT_PORT ?>; 
            
            attrs.enable<?=INTERFACE_TYPE_SNMP ?> = '';
            attrs.portdisplay<?=INTERFACE_TYPE_SNMP ?> = 'none';
            attrs.portnum<?=INTERFACE_TYPE_SNMP ?> = <?=BZHY_SNMP_DEFAULT_PORT ?>; 
            attrs.bulk = '';
            
            attrs.enable<?=INTERFACE_TYPE_JMX ?> = '';
            attrs.portdisplay<?=INTERFACE_TYPE_JMX ?> = 'none';
            attrs.portnum<?=INTERFACE_TYPE_JMX ?> = <?=BZHY_JMX_DEFAULT_PORT ?>; 
            
            attrs.enable<?=INTERFACE_TYPE_IPMI ?> = '';
            attrs.portdisplay<?=INTERFACE_TYPE_IPMI ?> = 'none';
            attrs.portnum<?=INTERFACE_TYPE_IPMI ?> = <?=BZHY_IPMI_DEFAULT_PORT ?>; 
            
            
            if(hostInterface.enable && hostInterface.enable[<?=INTERFACE_TYPE_AGENT ?>] == 1){
                attrs.enable<?=INTERFACE_TYPE_AGENT ?> = 'checked="checked"';
                attrs.portdisplay<?=INTERFACE_TYPE_AGENT ?> = '';
            }
            if(hostInterface.port && hostInterface.port[<?=INTERFACE_TYPE_AGENT ?>]){
                attrs.portnum<?=INTERFACE_TYPE_AGENT ?> = hostInterface.port[<?=INTERFACE_TYPE_AGENT ?>];
            }

            if(hostInterface.enable && hostInterface.enable[<?=INTERFACE_TYPE_SNMP ?>] == 1){
                attrs.enable<?=INTERFACE_TYPE_SNMP ?> = 'checked="checked"';
                attrs.portdisplay<?=INTERFACE_TYPE_SNMP ?> = '';
            }
            if(hostInterface.port && hostInterface.port[<?=INTERFACE_TYPE_SNMP ?>]){
                attrs.portnum<?=INTERFACE_TYPE_SNMP ?> = hostInterface.port[<?=INTERFACE_TYPE_SNMP ?>];
            }
            if(hostInterface.bulk){
                attrs.bulk = 'checked="checked"';
            }

            if(hostInterface.enable && hostInterface.enable[<?=INTERFACE_TYPE_JMX ?>] == 1){
                attrs.enable<?=INTERFACE_TYPE_JMX ?> = 'checked="checked"';
                attrs.portdisplay<?=INTERFACE_TYPE_JMX ?> = '';
            }
            if(hostInterface.port && hostInterface.port[<?=INTERFACE_TYPE_JMX ?>]){
                attrs.portnum<?=INTERFACE_TYPE_JMX ?> = hostInterface.port[<?=INTERFACE_TYPE_JMX ?>];
            }

            if(hostInterface.enable && hostInterface.enable[<?=INTERFACE_TYPE_IPMI ?>] == 1){
                attrs.enable<?=INTERFACE_TYPE_IPMI ?> = 'checked="checked"';
                attrs.portdisplay<?=INTERFACE_TYPE_IPMI ?> = '';
            }
            if(hostInterface.port && hostInterface.port[<?=INTERFACE_TYPE_IPMI ?>]){
                attrs.portnum<?=INTERFACE_TYPE_IPMI ?> = hostInterface.port[<?=INTERFACE_TYPE_IPMI ?>];
            }
            
			return attrs;
		}

		function getDomIdForRowInsert(hostInterfaceType) {
			var footerRowId;
            footerRowId = '#agentInterfacesFooter';
          
			return footerRowId;
		}

		function createNewHostInterface(hostInterfaceType) {
			var newInterface = {
				name:  'eth',
                                mask: '255.255.255.0',
                                isNew: true,
				useip: 1,
				type: getHostInterfaceNumericType(hostInterfaceType),
				port_agent: <?= BZHY_AGENT_DEFAULT_PORT ?>,
                port_snmp:<?=BZHY_SNMP_DEFAULT_PORT ?>,
                port_jmx:<?=BZHY_JMX_DEFAULT_PORT ?>,
                port_ipmi:<?=BZHY_IPMI_DEFAULT_PORT ?>,
				ip: '127.0.0.1'
			};

	
            
            newInterface.bulk = 1;
            
			newInterface.interfaceid = 1;
			while (allHostInterfaces[newInterface.interfaceid] !== void(0)) {
				newInterface.interfaceid++;
			}
            
            newInterface.name = newInterface.name + (newInterface.interfaceid - 1);
			addHostInterface(newInterface);

			return newInterface;
		}

		function addHostInterface(hostInterface) {
			allHostInterfaces[hostInterface.interfaceid] = hostInterface;
            maxInterfaceId = hostInterface.interfaceid;
		}

		function moveRowToAnotherTypeTable(hostInterfaceId, newHostInterfaceType) {
			var newDomId = getDomIdForRowInsert(newHostInterfaceType);

			jQuery('#interface_main_' + hostInterfaceId).attr('name', 'mainInterfaces[' + newHostInterfaceType + ']');
			jQuery('#interface_main_' + hostInterfaceId).prop('checked', false);
			jQuery('#interface_type_' + hostInterfaceId).val(newHostInterfaceType);
			jQuery('#hostInterfaceRow_' + hostInterfaceId).insertBefore(newDomId);
		}

		function resetUseipInterface(hostInterfaceId) {
			var useip = allHostInterfaces[hostInterfaceId].useip;
			if (useip == 0) {
				jQuery('#radio_dns_' + hostInterfaceId).prop('checked', true);
			}
			else {
				jQuery('#radio_ip_' + hostInterfaceId).prop('checked', true);
			}
		}

		return {
			add: function(hostInterfaces) {
				for (var i = 0; i < hostInterfaces.length; i++) {
					addHostInterface(hostInterfaces[i]);
					renderHostInterfaceRow(hostInterfaces[i]);
				}
			},

			addNew: function(type) {
				var hostInterface = createNewHostInterface(type);

				allHostInterfaces[hostInterface.interfaceid] = hostInterface;
				renderHostInterfaceRow(hostInterface);
			},

			remove: function(hostInterfaceId) {
				delete allHostInterfaces[hostInterfaceId];
			},

			setType: function(hostInterfaceId, typeName) {
				var newTypeNum = getHostInterfaceNumericType(typeName);

				if (allHostInterfaces[hostInterfaceId].type !== newTypeNum) {
					moveRowToAnotherTypeTable(hostInterfaceId, newTypeNum);
					allHostInterfaces[hostInterfaceId].type = newTypeNum;
					allHostInterfaces[hostInterfaceId].main = '0';
				}
			},

			setUseipForInterface: function(hostInterfaceId, useip) {
				allHostInterfaces[hostInterfaceId].useip = useip;
			},

			disable: function() {
				jQuery('.interface-drag-control, .interface-control').html('');
				jQuery('.interfaceRow').find('input')
					.removeAttr('id')
					.removeAttr('name');
				jQuery('.interfaceRow').find('input[type="text"]').attr('readonly', true);
				jQuery('.interfaceRow').find('input[type="radio"], input[type="checkbox"]').attr('disabled', true);
			}
		}
	}());

	jQuery(document).ready(function() {
		'use strict';

		jQuery('#hostlist').on('click', 'button.remove', function() {
			var interfaceId = jQuery(this).data('interfaceid');
			jQuery('#hostInterfaceRow_first_' + interfaceId).remove();
            jQuery('#hostInterfaceRow_second_' + interfaceId).remove();
			hostInterfacesManager.remove(interfaceId);
		});

		jQuery('#hostlist').on('click', 'input[type=radio].mainInterface', function() {
			var interfaceId = jQuery(this).val();
		});

		// when we start dragging row, all radio buttons are unchecked for some reason, we store radio buttons values
		// to restore them when drag is ended
		jQuery('#hostlist').on('click', 'input[type=radio].interface-useip', function() {
			var interfaceId = jQuery(this).attr('id').match(/\d+/);
			hostInterfacesManager.setUseipForInterface(interfaceId[0], jQuery(this).val());
		});

		jQuery('#tls_connect, #tls_in_psk, #tls_in_cert').change(function() {
			// If certificate is selected or checked.
			if (jQuery('input[name=tls_connect]:checked').val() == <?= HOST_ENCRYPTION_CERTIFICATE ?>
					|| jQuery('#tls_in_cert').is(':checked')) {
				jQuery('#tls_issuer, #tls_subject').closest('li').show();
			}
			else {
				jQuery('#tls_issuer, #tls_subject').closest('li').hide();
			}

			// If PSK is selected or checked.
			if (jQuery('input[name=tls_connect]:checked').val() == <?= HOST_ENCRYPTION_PSK ?>
					|| jQuery('#tls_in_psk').is(':checked')) {
				jQuery('#tls_psk, #tls_psk_identity').closest('li').show();
			}
			else {
				jQuery('#tls_psk, #tls_psk_identity').closest('li').hide();
			}
		});

		jQuery('#agentInterfaces, #SNMPInterfaces, #JMXInterfaces, #IPMIInterfaces').parent().droppable({
			tolerance: 'pointer',
			drop: function(event, ui) {
				var hostInterfaceTypeName = jQuery(this).data('type'),
					hostInterfaceId = ui.draggable.data('interfaceid');

				ui.helper.css({'left': '', 'top': ''});

				if (getHostInterfaceNumericType(hostInterfaceTypeName) == <?= INTERFACE_TYPE_SNMP ?>) {
					if (jQuery('.interface-bulk', jQuery('#hostInterfaceRow_' + hostInterfaceId)).length == 0) {
						var bulkDiv = jQuery('<div>', {
							'class': 'interface-bulk'
						});

						// append checkbox
						bulkDiv.append(jQuery('<input>', {
							id: 'interfaces[' + hostInterfaceId + '][bulk]',
							'class': 'input checkbox pointer',
							type: 'checkbox',
							name: 'interfaces[' + hostInterfaceId + '][bulk]',
							value: 1,
							checked: true
						}));

						// append label
						bulkDiv.append(jQuery('<label>', {
							'for': 'interfaces[' + hostInterfaceId + '][bulk]',
							text: '<?= _('Use bulk requests') ?>'
						}));

						jQuery('.interface-ip', jQuery('#hostInterfaceRow_' + hostInterfaceId)).append(bulkDiv);
					}
				}
				else {
					jQuery('.interface-bulk', jQuery('#hostInterfaceRow_' + hostInterfaceId)).remove();
				}

				hostInterfacesManager.setType(hostInterfaceId, hostInterfaceTypeName);
		//		hostInterfacesManager.resetMainInterfaces();
			},
			activate: function(event, ui) {
				if (!jQuery(this).find(ui.draggable).length) {
					jQuery(this).addClass('<?= ZBX_STYLE_DRAG_DROP_AREA ?>');
				}
			},
			deactivate: function(event, ui) {
				jQuery(this).removeClass('<?= ZBX_STYLE_DRAG_DROP_AREA ?>');
			}
		});

		jQuery('#addAgentInterface').on('click', function() {
			hostInterfacesManager.addNew('agent');
		});
		jQuery('#addSNMPInterface').on('click', function() {
			hostInterfacesManager.addNew('snmp');
		});
		jQuery('#addJMXInterface').on('click', function() {
			hostInterfacesManager.addNew('jmx');
		});
		jQuery('#addIPMIInterface').on('click', function() {
			hostInterfacesManager.addNew('ipmi');
		});
                
               // radio button of inventory modes was clicked
		jQuery('input[name=inventory_mode]').click(function() {
			// action depending on which button was clicked
			var inventoryFields = jQuery('#inventorylist :input:gt(2)');

			switch (jQuery(this).val()) {
				case '<?= HOST_INVENTORY_DISABLED ?>':
					inventoryFields.prop('disabled', true);
					jQuery('.populating_item').hide();
					break;
				case '<?= HOST_INVENTORY_MANUAL ?>':
					inventoryFields.prop('disabled', false);
					jQuery('.populating_item').hide();
					break;
				case '<?= HOST_INVENTORY_AUTOMATIC ?>':
					inventoryFields.prop('disabled', false);
					inventoryFields.filter('.linked_to_item').prop('disabled', true);
					jQuery('.populating_item').show();
					break;
			}
		});

		/**
		 * Mass update
		 */
		jQuery('#mass_replace_tpls').on('change', function() {
			jQuery('#mass_clear_tpls').prop('disabled', !this.checked);
		}).change();

		// Refresh field visibility on document load.
		if ((jQuery('#tls_accept').val() & <?= HOST_ENCRYPTION_NONE ?>) == <?= HOST_ENCRYPTION_NONE ?>) {
			jQuery('#tls_in_none').prop('checked', true);
		}
		if ((jQuery('#tls_accept').val() & <?= HOST_ENCRYPTION_PSK ?>) == <?= HOST_ENCRYPTION_PSK ?>) {
			jQuery('#tls_in_psk').prop('checked', true);
		}
		if ((jQuery('#tls_accept').val() & <?= HOST_ENCRYPTION_CERTIFICATE ?>) == <?= HOST_ENCRYPTION_CERTIFICATE ?>) {
			jQuery('#tls_in_cert').prop('checked', true);
		}

		jQuery('input[name=tls_connect]').trigger('change');

		// Depending on checkboxes, create a value for hidden field 'tls_accept'.
		jQuery('#hostForm').submit(function() {
			var tls_accept = 0x00;

			if (jQuery('#tls_in_none').is(':checked')) {
				tls_accept |= <?= HOST_ENCRYPTION_NONE ?>;
			}
			if (jQuery('#tls_in_psk').is(':checked')) {
				tls_accept |= <?= HOST_ENCRYPTION_PSK ?>;
			}
			if (jQuery('#tls_in_cert').is(':checked')) {
				tls_accept |= <?= HOST_ENCRYPTION_CERTIFICATE ?>;
			}

			jQuery('#tls_accept').val(tls_accept);
		});
	});

	function getHostInterfaceNumericType(typeName) {
		var typeNum;

		switch (typeName) {
			case 'agent':
				typeNum = '<?= INTERFACE_TYPE_AGENT ?>';
				break;
			case 'snmp':
				typeNum = '<?= INTERFACE_TYPE_SNMP ?>';
				break;
			case 'jmx':
				typeNum = '<?= INTERFACE_TYPE_JMX ?>';
				break;
			case 'ipmi':
				typeNum = '<?= INTERFACE_TYPE_IPMI ?>';
				break;
                        case'interface':
                                typeNum = '5';
                                break;
			default:
				throw new Error('Unknown host interface type name.');
		}
		return typeNum;
	}
</script>
