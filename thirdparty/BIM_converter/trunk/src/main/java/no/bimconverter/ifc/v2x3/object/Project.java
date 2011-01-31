package no.bimconverter.ifc.v2x3.object;

import java.lang.reflect.InvocationTargetException;
import java.lang.reflect.Method;
import java.util.ArrayList;
import java.util.List;

import javax.xml.bind.Element;
import javax.xml.bind.JAXBElement;
import javax.xml.bind.annotation.XmlAnyElement;
import javax.xml.bind.annotation.XmlElement;
import javax.xml.bind.annotation.XmlElementRef;
import javax.xml.bind.annotation.XmlElementRefs;
import javax.xml.bind.annotation.XmlElementWrapper;
import javax.xml.bind.annotation.XmlMixed;
import javax.xml.bind.annotation.XmlRootElement;
import javax.xml.namespace.QName;

import no.bimconverter.ifc.IfcSdaiException;
import no.bimconverter.ifc.jaxb.Decomposition;
import no.bimconverter.ifc.jaxb.Units;


import jsdai.SIfc2x3.AIfcunit;
import jsdai.SIfc2x3.EIfcbuilding;
import jsdai.SIfc2x3.EIfccurrencyenum;
import jsdai.SIfc2x3.EIfcderivedunit;
import jsdai.SIfc2x3.EIfcderivedunitenum;
import jsdai.SIfc2x3.EIfcmonetaryunit;
import jsdai.SIfc2x3.EIfcnamedunit;
import jsdai.SIfc2x3.EIfcobjectdefinition;
import jsdai.SIfc2x3.EIfcproject;
import jsdai.SIfc2x3.EIfcrelaggregates;
import jsdai.SIfc2x3.EIfcsite;
import jsdai.SIfc2x3.EIfcsiunitname;
import jsdai.SIfc2x3.EIfcunitassignment;
import jsdai.SIfc2x3.EIfcunitenum;
import jsdai.lang.AEntity;
import jsdai.lang.EEntity;
import jsdai.lang.SdaiException;
import jsdai.lang.SdaiIterator;
import jsdai.lang.SdaiModel;
import jsdai.util.LangUtils;
/*
 * there has to be exactly one project object in the exchange file
 */
@XmlRootElement
public class Project extends CommonObjectImpl{
	
	
	/*final public static String ATTRIBUTE_KEY_PHASE = "Phase";
	final public static String ATTRIBUTE_KEY_LONGNAME = "Long name";*/
	
	
	
	
	//private Map<String, String> units = new HashMap<String, String>();
	private Units units = new Units();
	private Decomposition decomposition = new Decomposition();
	private EIfcnamedunit unit;
	
	
	
	public Project() {
	}
	
	
	/*
	public void loadModel(SdaiModel model) {
		List<EEntity> projectList = super.loadModel(model, EIfcproject.class);
		int projectListSize = projectList.size();
		if(projectListSize == 0) {
			throw new IfcSdaiException("No projects found!");
		} else if (projectListSize > 1) {
			throw new IfcSdaiException("Too many projects found!");
		} else {
			EIfcproject theProject = (EIfcproject) projectList.get(0);
			try {
				this.loadAttributes(theProject);
				this.loadUnits(theProject);
				this.loadDecomposition(theProject);
				//this.loadOwnerHistory(theProject);
			} catch (SdaiException e) {
				throw new IfcSdaiException("Sdai error!", e);
			}
		}
	}
	*/
	
	@Override
	public void load(EIfcobjectdefinition object) {
		super.load(object);
		EIfcproject entity = (EIfcproject)object;
		try {
			this.loadAttributes(entity);
			this.loadUnits(entity);
			this.loadDecomposition(entity);
		} catch (SdaiException e) {
			e.printStackTrace();
		}
	}
	
	/*
	 * One site allowed, but optional
	 * Building must be given if no site
	 * Project can not have link to site and building
	 */
	private void loadDecomposition(EIfcproject theProject) throws SdaiException {
		List<EIfcobjectdefinition> projectIsDecomposedBy = this.getIsDecomposedBy(theProject);
		if(projectIsDecomposedBy.size() > 0 ) {
			List<EIfcobjectdefinition> buildings = new ArrayList<EIfcobjectdefinition>();
			boolean hasSite = false;
			for(EIfcobjectdefinition objectDefinition : projectIsDecomposedBy) {
				if(objectDefinition.isKindOf(EIfcsite.class)) {
					hasSite = true;
					
					this.decomposition.setSite(objectDefinition.getGlobalid(null));
					
					//this.decomposition.put(DECOMPOSITION_KEY_SITE,new String[] {guid});
					List<EIfcobjectdefinition> siteIsDecomposedBy = getIsDecomposedBy(objectDefinition);
					if(siteIsDecomposedBy.size() == 0) {
						throw new IfcSdaiException("There must be at least one building!");
					} else {
						addBuildings(siteIsDecomposedBy);
					}
				} else if (objectDefinition.isKindOf(EIfcbuilding.class) && hasSite) {
					throw new IfcSdaiException("Project can not have site and building!");
				} else if (objectDefinition.isKindOf(EIfcbuilding.class) && !hasSite) {
					buildings.add(objectDefinition);
				}
			}
			if(buildings.size()> 0) {
				addBuildings(buildings);
			}
		} else {
			throw new IfcSdaiException("Error with model! Missing site or building!");
		}
	}
	private void addBuildings(List<EIfcobjectdefinition> siteIsDecomposedBy)
			throws SdaiException {
		
		for ( EIfcobjectdefinition object : siteIsDecomposedBy) {
			if(object.isKindOf(EIfcbuilding.class)) {
				this.decomposition.addBuildingId(object.getGlobalid(null));
			}
		}
		if(this.decomposition.getBuildings().size() == 0) {
			throw new IfcSdaiException("There must be at least one building!");
		}
	}
	
	public Decomposition getDecomposition() {
		return decomposition;
	}


	public void setDecomposition(Decomposition decomposition2) {
		this.decomposition = decomposition;
	}


	private void loadAttributes(EIfcproject project) throws SdaiException {
		if(project.testLongname(null)) {
			//this.attributes.put(ATTRIBUTE_KEY_LONGNAME, project.getLongname(null));
			this.attributes.setLongName(project.getLongname(null));
		}
		if(project.testPhase(null)) {
			//this.attributes.put(ATTRIBUTE_KEY_PHASE, project.getPhase(null));
			this.attributes.setPhase(project.getPhase(null));
		}
	}
	/*
	 * Should be safe for 2x4 version
	 */
	private void loadUnits(EIfcproject project) throws SdaiException {
		EIfcunitassignment unitAssignment = project.getUnitsincontext(null);
		AIfcunit units = unitAssignment.getUnits(null);
		SdaiIterator unitsIterator = units.createIterator();
		while(unitsIterator.next()) {
			EEntity member = units.getCurrentMember(unitsIterator);
			if(member.isKindOf(EIfcnamedunit.class)) {
				unit = (EIfcnamedunit) member;
				String unitType = EIfcunitenum.toString(unit.getUnittype(null));
				String unitName = extractUnitName();
				this.units.addElement(unitType, unitName);
			} else if (member.isKindOf(EIfcderivedunit.class)) {
				//TODO  - complete derived unit handling!
				EIfcderivedunit unit = (EIfcderivedunit) member;
				String unitType = EIfcderivedunitenum.toString(unit.getUnittype(null));
				String unitName = "Derived unit";
				if(unit.testUserdefinedtype(null)) {
					unitName = unitName + ": " + unit.getUserdefinedtype(null);
				}
				this.units.addElement(unitType, unitName);
			} else if (member.isKindOf(EIfcmonetaryunit.class)) {
				EIfcmonetaryunit unit = (EIfcmonetaryunit) member;
				String unitType = "IfcMonetaryUnit";
				String unitName = EIfccurrencyenum.toString(unit.getCurrency(null));
				this.units.addElement(unitType, unitName);
			}
				
		}
	}


	private String extractUnitName() throws SdaiException {
		String unitName = null;
		Class<? extends EEntity> currentClass = unit.getClass();
		Method[] classMethods = currentClass.getDeclaredMethods();
		
		for(Method m : classMethods) {
			if(m.getName().startsWith("getName")) {
				
				try {
					Object result =  m.invoke(unit, unit);
					// currently, IFCSIUNIT is the only unit type that uses enum
					if(m.getReturnType().equals(int.class)) {
						int resultId = ((Integer)result).intValue();
						result = (String) EIfcsiunitname.toString(resultId);
					}
					unitName = String.valueOf(result);
				}catch (IllegalArgumentException e) {
					e.printStackTrace();
					throw new SdaiException();
				} catch (IllegalAccessException e) {
					e.printStackTrace();
					throw new SdaiException();
				} catch (InvocationTargetException e) {
					e.printStackTrace();
					throw new SdaiException();
				}
			}
			break;
		}
		return unitName;
	}
	/*
	private void loadUnits(EIfcproject project) throws SdaiException {
		EIfcunitassignment unitAssignment = project.getUnitsincontext(null);
		AIfcunit units = unitAssignment.getUnits(null);
		SdaiIterator unitsIterator = units.createIterator();
		while(unitsIterator.next()) {
			EEntity member = units.getCurrentMember(unitsIterator);
			unit = (EIfcnamedunit) member;
			if(checkUnitType(EIfcunitenum.AREAUNIT)) {
				extractUnitName(UNIT_KEY_AREA);
			} else if (checkUnitType(EIfcunitenum.LENGTHUNIT)) {
				extractUnitName(UNIT_KEY_LENGTH);
			} else if (checkUnitType(EIfcunitenum.VOLUMEUNIT)) {
				extractUnitName(UNIT_KEY_VOLUME);
			}
		}
	}
	private void extractUnitName(String hashMapKey) throws SdaiException {
		String label;
		if(unit.isKindOf(EIfcsiunit.class)) {
			EIfcsiunit unitWithType = (EIfcsiunit) unit;
			label = EIfcsiunitname.toString(unitWithType.getName(null));
		} else if (unit.isKindOf(EIfcconversionbasedunit.class)) {
			EIfcconversionbasedunit unitWithType = (EIfcconversionbasedunit) unit;
			label = unitWithType.getName(null);
		} else if (unit.isKindOf(EIfccontextdependentunit.class)) {
			EIfccontextdependentunit unitWithType = (EIfccontextdependentunit)unit;
			label = unitWithType.getName(null);
		} else {
			// Will not reach this under normal circumstances!
			throw new IfcSdaiException("Unknown unit type!"+unit);
		}
		this.units.put(hashMapKey, label);
	}
	private boolean checkUnitType(int unitType) throws SdaiException {
		return unit.getUnittype(null) == unitType;
	}
	*/
	

	
	public Units getUnits() {
		return units;
	}


	public void setUnits(Units units) {
		this.units = units;
	}
	
	public static void getIfcRepresentation(SdaiModel model, SdaiModel modelNew) throws SdaiException {
		AEntity projects =LangUtils.getInstancesOfEntity(model, model.getEntityDefinition("IfcProject"));
		AEntity sites = LangUtils.getInstancesOfEntity(model, model.getEntityDefinition("IfcSite"));
		
		AEntity agg = new AEntity();
		agg.addUnordered(projects.getByIndexEntity(1));
		agg.addUnordered(sites.getByIndexEntity(1));
		LangUtils.findRelated(agg, projects.getByIndexEntity(1));
		LangUtils.findRelated(agg, sites.getByIndexEntity(1));
		
		
		
		for(int i = 1; i <= agg.getMemberCount(); i++) {
			EEntity currentObject = agg.getByIndexEntity(i);
			if(currentObject.isKindOf(EIfcproject.class) || currentObject.isKindOf(EIfcsite.class)) {
				AEntity iu = new AEntity();
				currentObject.findEntityInstanceUsers(null, iu);
				for(int c = 1; c <= iu.getMemberCount(); c++) {
					EEntity currE = iu.getByIndexEntity(c);
					if(currE.isKindOf(EIfcrelaggregates.class)) {
						if(!agg.isMember(currE)) {
							agg.addUnordered(currE);
							LangUtils.findRelated(agg, currE);
						}
					}
				}
			}
		}
		
		
		modelNew.copyInstances(agg);
	}
	
	
	/*private List<JaxbElementTest<String>> objects = new ArrayList<JaxbElementTest<String>>();
	
	@XmlElementWrapper(name="vals")
	@XmlElement(name="unitd") 
	public List<JaxbElementTest<String>> getObjects() {
		//objects.add(new NameValuePair("name", "value"));
		//objects.add(new ClassificationItem("key", "name", "sysName", "sysEd"));
		objects.add(new JaxbElementTest<String>(new QName("aTest"), String.class, "aVal"));
		objects.add(new JaxbElementTest<String>(new QName("aTest2"), String.class, "aVal"));

	    return objects;
	}

	public void setObjects(List<JaxbElementTest<String>> objects) {
		this.objects = objects;
	}*/
	

	
	 
}
