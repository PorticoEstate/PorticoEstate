package no.bimfm.ifc.v2x3.object.element.type;

import javax.xml.bind.annotation.XmlRootElement;

import jsdai.SIfc2x3.EIfcdoorstyle;
import jsdai.SIfc2x3.EIfcdoorstyleconstructionenum;
import jsdai.SIfc2x3.EIfcdoorstyleoperationenum;
import jsdai.SIfc2x3.EIfcobject;
import jsdai.SIfc2x3.EIfcwindowstyle;
import jsdai.SIfc2x3.EIfcwindowstyleconstructionenum;
import jsdai.SIfc2x3.EIfcwindowstyleoperationenum;
import jsdai.lang.SdaiException;
import no.bimfm.ifc.v2x3.object.TypeObject;
@XmlRootElement
public class DoorStyle extends TypeObject{
	
	public void load(EIfcdoorstyle entity) {
		try {
			this.loadAttributes(entity);
			this.loadClassification(entity);
			this.loadMaterial(entity);
			this.loadTypeProperties(entity);
			//this.loadProperties(entity);
			/*
			this.loadClassification(entity);
			this.loadBaseQuantities(entity);
			this.loadProperties(entity);
			*/
		} catch (SdaiException e) {
			e.printStackTrace();
		}
	}
	
	private void loadAttributes(EIfcdoorstyle doorStyle) throws SdaiException {
		super.loadAttributes(doorStyle);
		this.attributes.setConstructionType(EIfcdoorstyleconstructionenum.toString(doorStyle.getConstructiontype(null)));
		this.attributes.setOperationType(EIfcdoorstyleoperationenum.toString(doorStyle.getOperationtype(null)));


//		EIfcdoorstyleconstructionenum.toString(doorStyle.getOperationtype(arg0));
//		this.attributes.put(Attribute.CONSTRUCTION_TYPE.key, EIfcwindowstyleconstructionenum.toString(doorStyle.getConstructiontype(null)));
//		this.attributes.put(Attribute.OPERATION_TYPE.key, EIfcwindowstyleoperationenum.toString(doorStyle.getOperationtype(null)));
	}
	public enum Attribute {
		CONSTRUCTION_TYPE("Construction type"),
		OPERATION_TYPE("Operation type");
		private final String key;
		Attribute(String key) {
	        this.key = key;
	    }
		public String getKey() {
			return key;
		}
	}

}
