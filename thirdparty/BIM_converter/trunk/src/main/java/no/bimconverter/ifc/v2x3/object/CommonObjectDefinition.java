package no.bimconverter.ifc.v2x3.object;

import java.lang.reflect.InvocationTargetException;
import java.lang.reflect.Method;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import javax.xml.bind.annotation.XmlAttribute;

import no.bimconverter.ifc.jaxb.Attributes;
import no.bimconverter.ifc.jaxb.NameValuePair;

import jsdai.SIfc2x3.AIfcobjectdefinition;
import jsdai.SIfc2x3.AIfcphysicalquantity;
import jsdai.SIfc2x3.AIfcproperty;
import jsdai.SIfc2x3.AIfcreldecomposes;
import jsdai.SIfc2x3.AIfcreldefines;
import jsdai.SIfc2x3.EIfccomplexproperty;
import jsdai.SIfc2x3.EIfcelementquantity;
import jsdai.SIfc2x3.EIfcobject;
import jsdai.SIfc2x3.EIfcobjectdefinition;
import jsdai.SIfc2x3.EIfcphysicalquantity;
import jsdai.SIfc2x3.EIfcproperty;
import jsdai.SIfc2x3.EIfcpropertyset;
import jsdai.SIfc2x3.EIfcpropertysetdefinition;
import jsdai.SIfc2x3.EIfcpropertysinglevalue;
import jsdai.SIfc2x3.EIfcquantityarea;
import jsdai.SIfc2x3.EIfcquantitycount;
import jsdai.SIfc2x3.EIfcquantitylength;
import jsdai.SIfc2x3.EIfcquantitytime;
import jsdai.SIfc2x3.EIfcquantityvolume;
import jsdai.SIfc2x3.EIfcquantityweight;
import jsdai.SIfc2x3.EIfcreldecomposes;
import jsdai.SIfc2x3.EIfcreldefines;
import jsdai.SIfc2x3.EIfcreldefinesbyproperties;
import jsdai.SIfc2x3.EIfcroot;
import jsdai.SIfc2x3.EIfcsimpleproperty;
import jsdai.dictionary.EAttribute;
import jsdai.lang.AEntity;
import jsdai.lang.A_integer;
import jsdai.lang.A_string;
import jsdai.lang.EEntity;
import jsdai.lang.SdaiException;
import jsdai.lang.SdaiIterator;
import jsdai.lang.SdaiModel;
import jsdai.lang.Value;


public class CommonObjectDefinition {
	final public static String ATTRIBUTE_KEY_GUID = "guid";
	final public static String ATTRIBUTE_KEY_NAME = "Name";
	
	final public static String ATTRIBUTE_KEY_DESCRIPTION = "Description";
	//protected Map<String, String> attributes = new HashMap<String, String>();
	protected Attributes attributes;// = new Attributes();
	
	private String ifcObjectType;
	private AIfcreldecomposes decomposesAggregation;
	protected Map<String, Map<String, String>> propertiesList;
	protected Map<String, Map<String, String>> quantitiesList;
	SdaiIterator integerIterator;
	
	public CommonObjectDefinition() {
	}
	
	protected void loadAttributes(EIfcroot entity) throws SdaiException {
		//first two are required
		this.initializeAttributes();
		//this.attributes.put(ATTRIBUTE_KEY_GUID, entity.getGlobalid(null));
		//this.attributes.put(ATTRIBUTE_KEY_NAME, entity.getName(null));
		this.attributes.setGuid(entity.getGlobalid(null));
		if(entity.testName(null)){
			this.attributes.setName(entity.getName(null));
		}
		
		if(entity.testDescription(null)){
			//this.attributes.put(ATTRIBUTE_KEY_DESCRIPTION, entity.getDescription(null));
			this.attributes.setDescription(entity.getDescription(null));
		}
		this.ifcObjectType = entity.getInstanceType().getName(null);
	}
	protected void initializeAttributes() {
		if(this.attributes == null) {
			this.attributes = new Attributes();
		}
	}
	/*
	 * takes care of EIfcreldefinesbyproperties
	 * Missing: take care of EIfcreldefinesbytype
	 */
	protected void relateObjectPropertiesAndQuantities(EIfcobject site) throws SdaiException {
		AIfcreldefines propertyRelations = site.getIsdefinedby(null, null);
		SdaiIterator propertyRelationsIterator = propertyRelations.createIterator();
		while(propertyRelationsIterator.next()) {
			EIfcreldefines propertyRelation = propertyRelations.getCurrentMember(propertyRelationsIterator);
			if(propertyRelation.isKindOf(EIfcreldefinesbyproperties.class)) {
				assignRelatedDefinitions(propertyRelation);
			}
		}
	}
	private void assignRelatedDefinitions(EIfcreldefines propertyRelation)
			throws SdaiException {
		EIfcreldefinesbyproperties relDefinesByProperties = (EIfcreldefinesbyproperties) propertyRelation;
		EIfcpropertysetdefinition propertydefinition =  relDefinesByProperties.getRelatingpropertydefinition(null);
		if(propertydefinition != null && propertydefinition.isKindOf(EIfcpropertyset.class)) {
			addPropertiesToPropertyList(propertydefinition);
		} else if(propertydefinition != null && propertydefinition.isKindOf(EIfcelementquantity.class)) {
			addQuantitiesToQuantityList(propertydefinition);
		}
	}
	private void addQuantitiesToQuantityList(
			EIfcpropertysetdefinition propertydefinition) throws SdaiException {
		initializeQuantitiesList();
		EIfcelementquantity elementQuantity = (EIfcelementquantity) propertydefinition;
		AIfcphysicalquantity elementQuantityAggregate = elementQuantity.getQuantities(null);
		SdaiIterator elementQuantityIterator = elementQuantityAggregate.createIterator();
		Map<String, String> physicalQuantityList = new HashMap<String, String>();
		while(elementQuantityIterator.next()) {
			EIfcphysicalquantity physicalQuantity = elementQuantityAggregate.getCurrentMember(elementQuantityIterator);
			//following line of code not used for time being, would need include both the name and type of item
			//List<NameValuePair> nvpList = extractSingleValueAttributes(physicalQuantity);
			
			//physicalQuantityList.put(nvpList.get(0).name, nvpList.get(0).value);
			
			
			/*
			 * This bit of code is trusting the IFC to correctly designate with a name the contents
			 * of IFCQUANTITY... items.. which might be a little dubious
			 */
			if(physicalQuantity.isKindOf(EIfcquantitylength.class)) {
				physicalQuantityList.put(physicalQuantity.getName(null), String.valueOf(((EIfcquantitylength)physicalQuantity).getLengthvalue(null)));
				
			} else if (physicalQuantity.isKindOf(EIfcquantityarea.class)) {
				physicalQuantityList.put(physicalQuantity.getName(null), String.valueOf(((EIfcquantityarea)physicalQuantity).getAreavalue(null)));
			} else if (physicalQuantity.isKindOf(EIfcquantityvolume.class)) {
				physicalQuantityList.put(physicalQuantity.getName(null), String.valueOf(((EIfcquantityvolume)physicalQuantity).getVolumevalue(null)));
			}  else if (physicalQuantity.isKindOf(EIfcquantitycount.class)) {
				physicalQuantityList.put(physicalQuantity.getName(null), String.valueOf(((EIfcquantitycount)physicalQuantity).getCountvalue(null)));
			} else if (physicalQuantity.isKindOf(EIfcquantityweight.class)) {
				physicalQuantityList.put(physicalQuantity.getName(null), String.valueOf(((EIfcquantityweight)physicalQuantity).getWeightvalue(null)));
			} else if (physicalQuantity.isKindOf(EIfcquantitytime.class)) {
				physicalQuantityList.put(physicalQuantity.getName(null), String.valueOf(((EIfcquantitytime)physicalQuantity).getTimevalue(null)));
			}
			
		}
		quantitiesList.put(elementQuantity.getName(null), physicalQuantityList);
	}
	private void initializeQuantitiesList() {
		if(this.quantitiesList == null) {
			this.quantitiesList = new  HashMap<String, Map<String, String>>();
		}
	}
	private void initializePropertiesList() {
		if(this.propertiesList == null) {
			this.propertiesList = new  HashMap<String, Map<String, String>>();
		}
	}
	protected void addPropertiesToPropertyList(
			EIfcpropertysetdefinition propertydefinition) throws SdaiException {
		this.initializePropertiesList();
		EIfcpropertyset propertySet = (EIfcpropertyset) propertydefinition;
		AIfcproperty propertiesAggregation = propertySet.getHasproperties(null);
		propertiesList.put(propertySet.getName(null), extractProperties(propertiesAggregation));
	}
	private HashMap<String, String> extractProperties(AIfcproperty propertiesAggregation) throws SdaiException {
		HashMap<String, String> propertiesMap = new HashMap<String, String>();
		for(int i = 1; i < propertiesAggregation.getMemberCount()+1; i++) {
			EIfcproperty property = propertiesAggregation.getByIndex(i);
			
			String value = getPropertyValue(property);
			propertiesMap.put(property.getName(null), value);
		}
		return propertiesMap;
	}
	private String getPropertyValue(EIfcproperty property) throws SdaiException {
		if(property.isKindOf(EIfccomplexproperty.class)) {
			StringBuilder propertyStringBuilder = new StringBuilder();
			EIfccomplexproperty complexProperty = (EIfccomplexproperty) property;
			AIfcproperty properties = complexProperty.getHasproperties(null);
			SdaiIterator propertiesIterator = properties.createIterator();
			while(propertiesIterator.next()) {
				EIfcproperty currentProperty = properties.getCurrentMember(propertiesIterator);
				propertyStringBuilder.append(currentProperty.getName(null)+",");
			}
			return propertyStringBuilder.toString();
		} else if (property.isKindOf(EIfcsimpleproperty.class)) {
			if(property.isKindOf(EIfcpropertysinglevalue.class)) {
				EIfcpropertysinglevalue propertySingle = (EIfcpropertysinglevalue) property;
				//doing some strange stuff here, the alternative is very complex
				if(propertySingle.testNominalvalue(null) != 0) {
					EAttribute nominalValueAttribute = propertySingle.getAttributeDefinition("nominalvalue");
					Value nominalValueAttributeValue = propertySingle.get(nominalValueAttribute);
					String nominalValueString = nominalValueAttributeValue.toString();
					String nominalValue = nominalValueString.split(" := ")[1];
					return nominalValue;
				}
				/*
				yo.testNominalvalue(null);
				System.out.println(yo.testNominalvalue(null));
				
				if(yo.testNominalvalue(null) == 29) {
					EIfclabel yo2 = new EIfclabel() {
					};
					System.out.println(yo.getNominalvalue(null, yo2));
					
					AAttribute attribs = yo.getInstanceType().getAttributes(null, null);
					for(int c = 1;c< attribs.getMemberCount() +1; c++) {
						System.out.println(attribs.getByIndex(c));
					}
					
					EAttribute attri = yo.getAttributeDefinition("nominalvalue");
					Object obj = yo.get_object(attri);
					Value lala = yo.get(attri);
					
					System.out.println("Info:"+lala);
				}
				*/
				
				return null;
			}
		}
		return null;
	}
	
	protected List<EIfcobjectdefinition> getIsDecomposedBy(EIfcobjectdefinition objectDefinition) throws SdaiException {
		List<EIfcobjectdefinition> entityList = new ArrayList<EIfcobjectdefinition>();
		decomposesAggregation = objectDefinition.getIsdecomposedby(null, null);
		retrieveRelatedObjects(entityList);
		return entityList;
	}
	protected EIfcobjectdefinition getDecomposes(EIfcobjectdefinition objectDefinition) throws SdaiException {
		List<EIfcobjectdefinition> entityList = new ArrayList<EIfcobjectdefinition>();
		decomposesAggregation = objectDefinition.getDecomposes(null, null);
		retrieveRelatingMembers(entityList);
		if(entityList.size()>0) {
			
			return entityList.get(0);
		} else {
			return null;
		}
	}
	
	protected List<EEntity> getParentEntities(EIfcobjectdefinition entity) throws SdaiException {
		List<EEntity> parents = new ArrayList<EEntity>();
		EIfcobjectdefinition parentEntity = entity;
		while( (parentEntity = this.getDecomposes(parentEntity)) != null) {
			parents.add(parentEntity);
		}
		return parents;
	}
	
	private void retrieveRelatingMembers(List<EIfcobjectdefinition> entityList) throws SdaiException {
		SdaiIterator isDecomposedByIterator = decomposesAggregation.createIterator();
		while(isDecomposedByIterator.next()) {
			EIfcreldecomposes relDecomposes = decomposesAggregation.getCurrentMember(isDecomposedByIterator);
			EIfcobjectdefinition objectDefinitionAggregate = relDecomposes.getRelatingobject(null);
			entityList.add(objectDefinitionAggregate);
		}
	}
	private void retrieveRelatedObjects(List<EIfcobjectdefinition> entityList)
			throws SdaiException {
		SdaiIterator isDecomposedByIterator = decomposesAggregation.createIterator();
		while(isDecomposedByIterator.next()) {
			EIfcreldecomposes relDecomposes = decomposesAggregation.getCurrentMember(isDecomposedByIterator);
			AIfcobjectdefinition objectDefinitionAggregate = relDecomposes.getRelatedobjects(null);
			SdaiIterator objectDefinitionIterator = objectDefinitionAggregate.createIterator();
			while(objectDefinitionIterator.next()) {
				EIfcobjectdefinition innerObjectDefinition = objectDefinitionAggregate.getCurrentMember(objectDefinitionIterator);
				entityList.add(innerObjectDefinition);
			}
		}
	}
	

	public List<NameValuePair> extractSingleValueAttributes(EEntity propertySetDefinition)
	throws SdaiException {
		List<NameValuePair> listOfProperties = new ArrayList<NameValuePair>();
		Class<? extends EEntity> currentClass = propertySetDefinition.getClass();
		Method[] classMethods = currentClass.getDeclaredMethods();
		
		
		
		for(Method m : classMethods) {
			if(m.getName().startsWith("test")) {
				String variableName = m.getName().replaceFirst("test", "");
				if(this.checkTestParameter(m, propertySetDefinition)) {
					try {
						if((Boolean) m.invoke(propertySetDefinition, propertySetDefinition)) {
							
							
							Method getMethod = this.getGetMethod(m.getName(), classMethods);
							
							
							if(this.checkTestParameter(getMethod, propertySetDefinition)) {
								Object result = getMethod.invoke(propertySetDefinition, propertySetDefinition);
								//basic error checking, most of the output will be 'primitive' objects 
								if(result.getClass().getPackage().getName().equals("java.lang")) {
									NameValuePair nvp = new NameValuePair(variableName, String.valueOf(result));
									//propertyList.getProperties().add(nvp);
									listOfProperties.add(nvp);
								} else if( result instanceof A_string) {
									NameValuePair nvp = new NameValuePair(variableName, this.getStringListAsString((A_string) result));
									listOfProperties.add(nvp);
								}
							}
						}
					} catch (IllegalArgumentException e) {
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
			}
			
		}
		return listOfProperties;
	}
	

	Method getGetMethod(String testMethod, Method[] methodArray) {
		String signature = "get"+testMethod.replaceFirst("test", "");
		for(Method m : methodArray) {
			if(m.getName().equals(signature)) {
				return m;
			}
		}
		return null;
	}
	/*
	* input parameter is usually the same type as the class itself that contains the method
	*/
	boolean checkTestParameter(Method m, Object parentClass) {
		Class<?>[] parameterTypes = m.getParameterTypes();
		if(parameterTypes.length == 1) {
			//System.out.println("length is 1,"+parentClass.getClass().getName()+", "+parameterTypes[0].getName());
			
			if(parameterTypes[0].isAssignableFrom(parentClass.getClass())) {
				return true;
			}
		}
		return false;
	}
	
	
	protected String getStringListAsString(A_string stringAggregation) throws SdaiException {
		StringBuilder resultingString = new StringBuilder();
		if(stringAggregation.getMemberCount() > 0) {
			integerIterator = stringAggregation.createIterator();
			while(integerIterator.next()) {
				resultingString.append(stringAggregation.getCurrentMember(integerIterator)+";");
			}
			resultingString.deleteCharAt(resultingString.length()-1);
		}
		return resultingString.toString();
	}

	protected String getIntListAsString(A_integer integerAggregation) throws SdaiException {
		StringBuilder resultingString = new StringBuilder();
		if(integerAggregation.getMemberCount() > 0) {
			integerIterator = integerAggregation.createIterator();
			while(integerIterator.next()) {
				resultingString.append(integerAggregation.getCurrentMember(integerIterator)+";");
			}
			resultingString.deleteCharAt(resultingString.length()-1);
		}
		return resultingString.toString();
	}
	public Attributes getAttributes() {
		return attributes;
	}
	public void setAttributes(Attributes attributes2) {
		this.attributes = attributes2;
	}
	@XmlAttribute
	public String getIfcObjectType() {
		return ifcObjectType;
	}
	public void setIfcObjectType(String ifcObjectType) {
		this.ifcObjectType = ifcObjectType;
	}
}
