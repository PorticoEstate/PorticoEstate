package no.bimfm.v2x3.loader;

import java.util.ArrayList;
import java.util.List;

import jsdai.SIfc2x3.EIfcobject;
import jsdai.SIfc2x3.EIfcwindow;
import jsdai.lang.AEntity;
import jsdai.lang.EEntity;
import jsdai.lang.SdaiException;
import jsdai.lang.SdaiIterator;
import jsdai.lang.SdaiModel;
import no.bimfm.ifc.IfcSdaiException;
import no.bimfm.ifc.v2x3.object.CommonObject;
import no.bimfm.ifc.v2x3.object.CommonObjectImpl;
import no.bimfm.ifc.v2x3.object.element.Window;
/*
 * Class that helps in loading IFC objects. It takes the IFC model,
 * searches for specific objects and applies the java class to process them
 */
public class CommonLoader {
	public CommonLoader() {
		
	}
	
	public List<EEntity> getEntitiesOfType(SdaiModel model, Class<? extends EEntity> IfcObjectType) {
		List<EEntity> objectList = new ArrayList<EEntity>();
		try {
			AEntity entities = model.getInstances();
			SdaiIterator entitiesIter = entities.createIterator();
			EEntity entity = null;
			while ( entitiesIter.next()) {
				entity = entities.getCurrentMemberEntity(entitiesIter);
				if(entity.isKindOf(IfcObjectType)){
					objectList.add(entity);
				}
			}
			return objectList;
		} catch (SdaiException e) {
			e.printStackTrace();
			throw new IfcSdaiException("Sdai exception!"+e.getMessage(), e);
		}
		
	}
	public List<? extends CommonObject> load(SdaiModel model, Class<? extends EEntity> IfcObjectType, Class<? extends CommonObject> objectClass) {
		List<EEntity> entityList = getEntitiesOfType(model, IfcObjectType);
		if(entityList.size() == 0) {
			throw new IfcSdaiException("Error: No elements found!");
		} else {
			List<CommonObject> spaces = new ArrayList<CommonObject>();
			for(EEntity currentEntity: entityList) {
				spaces.add(loadObject(currentEntity, objectClass));
			}
			return spaces;
		}
	}
	private CommonObject loadObject(EEntity entity, Class<? extends CommonObject> myObject) {
		CommonObject window;
		try {
			window = myObject.newInstance();
			window.load((EIfcobject)entity);
			return window;
		} catch (InstantiationException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
			
		} catch (IllegalAccessException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		return null;
	}
	
	
	/*public List<? extends CommonIfcObject> load(SdaiModel model) {
		List<EEntity> entityList = super.load(model, windowClass);
		List<CommonIfcObject> spaces = new ArrayList<CommonIfcObject>();
			for(EEntity currentEntity: entityList) {
				spaces.add(loadObject(currentEntity));
			}
		return spaces;
	}*/
}
