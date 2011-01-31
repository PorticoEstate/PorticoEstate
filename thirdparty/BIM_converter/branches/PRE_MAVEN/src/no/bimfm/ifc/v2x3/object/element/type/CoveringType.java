package no.bimfm.ifc.v2x3.object.element.type;

import javax.xml.bind.annotation.XmlRootElement;

import no.bimfm.ifc.v2x3.object.TypeObject;

import jsdai.SIfc2x3.EIfccoveringtype;
import jsdai.SIfc2x3.EIfccoveringtypeenum;
import jsdai.lang.SdaiException;
@XmlRootElement
public class CoveringType extends TypeObject{
	
	public void load(EIfccoveringtype entity) {
		try {
			this.loadAttributes(entity);
			this.loadClassification(entity);
			this.loadMaterial(entity);
			this.loadTypeProperties(entity);
		} catch (SdaiException e) {
			e.printStackTrace();
		}
	}
	
	private void loadAttributes(EIfccoveringtype coveringtype) throws SdaiException {
		super.loadAttributes(coveringtype);
		if(coveringtype.testPredefinedtype(null)) {
			int predefinedType = coveringtype.getPredefinedtype(null);
			//this.attributes.put(Attribute.PREDEFINED_COVERING_TYPE.key, EIfccoveringtypeenum.toString(predefinedType));
			this.attributes.setPredefinedCoveringType(EIfccoveringtypeenum.toString(predefinedType));
		}
	}
	
	
	public enum Attribute {
		PREDEFINED_COVERING_TYPE("Predefined covering type");
		private final String key;
		Attribute(String key) {
	        this.key = key;
	    }
		public String getKey() {
			return key;
		}
	}

}
