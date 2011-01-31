package no.bimfm.ifc.v2x3.object;

import java.util.List;
import java.util.Map;

import no.bimfm.ifc.v2x3.object.element.CommonElement;
import no.bimfm.jaxb.NameValuePair;
import no.bimfm.jaxb.PropertyList;

import jsdai.SIfc2x3.AIfcpropertysetdefinition;
import jsdai.SIfc2x3.EIfcpropertysetdefinition;
import jsdai.SIfc2x3.EIfctypeobject;
import jsdai.SIfc2x3.EIfcwindowpaneloperationenum;
import jsdai.SIfc2x3.EIfcwindowpanelpositionenum;
import jsdai.lang.SdaiException;
import jsdai.lang.SdaiIterator;

public class TypeObject extends CommonElement {

	protected void loadTypeProperties(EIfctypeobject typeObject) throws SdaiException {
				if(typeObject.testHaspropertysets(null))  {
					
					AIfcpropertysetdefinition propertySetsAggregate = typeObject.getHaspropertysets(null);
					SdaiIterator propertySetsIterator = propertySetsAggregate.createIterator();
					while(propertySetsIterator.next()) {
						EIfcpropertysetdefinition propertySetDefinition = propertySetsAggregate.getCurrentMember(propertySetsIterator);
						//Object yo = ((EIfcwindowpanelproperties)propertySetDefinition).
						PropertyList propertyList = new PropertyList();
						if(propertySetDefinition.testName(null)) {
							propertyList.setName(propertySetDefinition.getName(null));
						}
						propertyList.setType(propertySetDefinition.getInstanceType().getName(null));
						
						List<NameValuePair> listOfProperties = extractSingleValueAttributes(propertySetDefinition);
						for(NameValuePair nvp : listOfProperties) {
							propertyList.addElement(nvp.name, nvp.value);
						}
						//propertyList.setProperties(listOfProperties);
						initializeProperties();
						
						this.properties.add(propertyList);
					}
				}
				this.addEnumsToProperties();
			}
	/*
	 * might be possible to automate this..
	 */
	private void addEnumsToProperties() {
		if(this.properties != null) {
			for(PropertyList propList : this.properties) {
				if (propList.getType().equals("ifcwindowpanelproperties")) {
					/*
					List<NameValuePair> propertyList = propList.getProperties();
					for(NameValuePair nvp : propertyList) {
						if(nvp.name.equals("Operationtype") && nvp.value.matches("\\d\\d?")) {
							nvp.value = EIfcwindowpaneloperationenum.toString(Integer.parseInt(nvp.value));
						} else if(nvp.name.equals("Panelposition") && nvp.value.matches("\\d\\d?")) {
							nvp.value = EIfcwindowpanelpositionenum.toString(Integer.parseInt(nvp.value));
						} 
					}
					*/
					
					Map<String,String> propertyMap = propList.getElementMap();
					
					if(propertyMap.containsKey("Operationtype") && propertyMap.get("Operationtype").matches("\\d\\d?")) {
						propList.changeElementValue("Operationtype", EIfcwindowpaneloperationenum.toString(Integer.parseInt(propertyMap.get("Operationtype"))));
						//propertyMap.put("Operationtype", EIfcwindowpaneloperationenum.toString(Integer.parseInt(propertyMap.get("Operationtype"))));
					}
					if(propertyMap.containsKey("Panelposition") && propertyMap.get("Panelposition").matches("\\d\\d?")) {
						propList.changeElementValue("Panelposition", EIfcwindowpanelpositionenum.toString(Integer.parseInt(propertyMap.get("Panelposition"))));
						//propertyMap.put("Operationtype", EIfcwindowpanelpositionenum.toString(Integer.parseInt(propertyMap.get("Operationtype"))));
					}
					
				}
			}
		}
		
	}
	
	
}
