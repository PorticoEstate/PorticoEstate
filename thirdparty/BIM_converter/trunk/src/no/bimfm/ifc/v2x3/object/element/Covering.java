package no.bimfm.ifc.v2x3.object.element;

import javax.xml.bind.annotation.XmlRootElement;

import no.bimfm.ifc.v2x3.object.element.type.CoveringType;

import jsdai.SIfc2x3.EIfccovering;
import jsdai.SIfc2x3.EIfccoveringtype;
import jsdai.SIfc2x3.EIfccoveringtypeenum;
import jsdai.SIfc2x3.EIfcobject;
import jsdai.SIfc2x3.EIfcobjectdefinition;
import jsdai.SIfc2x3.EIfcspace;
import jsdai.SIfc2x3.EIfctypeobject;
import jsdai.lang.SdaiException;
@XmlRootElement
public class Covering extends CommonElement {
	final public static String commonPropertyName = "Pset_CoveringCommon";
	private CoveringType coveringType = null;
	public Covering() {
		super();
	}
	@Override
	public void load(EIfcobjectdefinition object) {
		super.load(object);
		EIfccovering entity = (EIfccovering)object;
		try {
			this.loadAttributes(entity);
			this.loadCoveringType(entity);
			this.loadParentItemsIntoSpatialContainer(entity, EIfcspace.class);
		} catch (SdaiException e) {
			e.printStackTrace();
		}
	}
//	public void load(EIfccovering entity) {
//		try {
//			this.loadAttributes(entity);
//			this.loadCoveringType(entity);
//			this.loadMaterial(entity);
//			this.loadClassification(entity);
//			this.loadBaseQuantities(entity);
//			this.loadProperties(entity);
//			this.loadParentItemsIntoSpatialContainer(entity, EIfcspace.class);
//		} catch (SdaiException e) {
//			e.printStackTrace();
//		}
//	}
	
	private void loadCoveringType(EIfccovering entity) throws SdaiException {
		EIfctypeobject typeObject = super.getTypeObject(entity);
		if(typeObject != null && typeObject.isKindOf(EIfccoveringtype.class)) {
			this.coveringType = new CoveringType();
			this.coveringType.load((EIfccoveringtype) typeObject);
			
		}
	}
	private void loadAttributes(EIfccovering covering) throws SdaiException {
		//super.loadAttributes(covering);
		if(covering.testPredefinedtype(null)) {
			int predefinedType = covering.getPredefinedtype(null);
			this.attributes.setPredefinedCoveringType( EIfccoveringtypeenum.toString(predefinedType));//put(Attribute.PREDEFINED_COVERING_TYPE.key, EIfccoveringtypeenum.toString(predefinedType));
		}
	}
	
	public CoveringType getCoveringType() {
		return coveringType;
	}
	public void setCoveringType(CoveringType coveringType) {
		this.coveringType = coveringType;
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
