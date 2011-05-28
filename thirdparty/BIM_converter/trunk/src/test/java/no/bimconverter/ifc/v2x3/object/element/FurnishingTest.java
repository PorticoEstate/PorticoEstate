package no.bimconverter.ifc.v2x3.object.element;


import static junit.framework.Assert.assertNotNull;
import static org.junit.Assert.assertEquals;

import java.util.HashMap;
import java.util.List;
import java.util.Map;

import javax.xml.bind.JAXBContext;
import javax.xml.bind.JAXBException;
import javax.xml.bind.Marshaller;


import no.bimconverter.ifc.IfcTestMethods;
import no.bimconverter.ifc.Repositories;
import no.bimconverter.ifc.RepositoriesImpl;
import no.bimconverter.ifc.jaxb.Attributes;
import no.bimconverter.ifc.jaxb.Decomposition;
import no.bimconverter.ifc.jaxb.NameValuePair;
import no.bimconverter.ifc.jaxb.PropertyList;
import no.bimconverter.ifc.v2x3.IfcModelImpl;

import org.junit.After;
import org.junit.Before;
import org.junit.Test;

public class FurnishingTest extends IfcTestMethods{

	
	int numberOfIfcElements = 1420;
	int numberOfIfcModels = 0;
	
	private List<Furnishing> furnishings;
	private Furnishing furnishing1;
	private Furnishing furnishing2;
	private Furnishing furnishing3;
	
	
	Attributes attributes = null;
	Attributes testAttributes = null;
	
	
	List<PropertyList> windowStyleproperties = null;
	
	List<NameValuePair> nvpListBeingTested = null;
	Map<String, String> propertyMapBeingTested = null;
	String propertySetShouldBeOfType = null;
	String propertySetShouldHaveName = null;
	
	private Map<String, String> mapBasedTestData;
	private PropertyList currentPropertyList;

	
	@Before
	public void setUp() {
		super.createTestRepo();
		furnishings = (List<Furnishing>) model.getFacilityManagementEntity(new Furnishing());
		furnishing1 = furnishings.get(0);
		furnishing2 = furnishings.get(1);
		furnishing3 = furnishings.get(2);
	}
	@After
	public void tearDown() {
		
	}
	
	@Test
	public void testWindowObject() throws JAXBException {
		assertEquals(3, furnishings.size()); 
		assertNotNull(furnishing1);
	}
	
	public void testDisplayFurnishing(Furnishing theFurnishing) throws JAXBException {
		assertNotNull(theFurnishing);
		//super.outputXmlToSystemOut(theFurnishing);
	}
	@Test
	public void testDisplayFurnishings() throws JAXBException {
		testDisplayFurnishing(furnishing1);
		testDisplayFurnishing(furnishing2);
		testDisplayFurnishing(furnishing3);
	}
	
	@Test
	public void testAttributes() {
		attributes = furnishing1.getAttributes();
		this.testAttributes = new Attributes();
		this.testAttributes.setGuid("0h$ksovXH3Jeg0dd2dsfaf");
		this.testAttributes.setName("Kitchenette:EG.002");
		this.testAttributes.setDescription("Kitchenette-001 object for testing Basic FM Handover");
		
		this.testBasicAttributes();
		
		attributes = furnishing2.getAttributes();
		this.testAttributes = new Attributes();
		this.testAttributes.setGuid("0h$ksovsQ3Jeg0dd2dsfaf");
		this.testAttributes.setName("Cupboard(1):EG.002");
		this.testAttributes.setDescription("Cupboard-001 for testing Basic FM Handover");
		
		this.testBasicAttributes();
		
		attributes = furnishing3.getAttributes();
		this.testAttributes = new Attributes();
		this.testAttributes.setGuid("0h$ksovsQ3Jerfdd2dsfaf");
		this.testAttributes.setName("Cupboard(2):EG.002");
		this.testAttributes.setDescription("Cupboard-002 for testing Basic FM Handover");
		
		this.testBasicAttributes();
	}
	public void testBasicAttributes() {
		assertEquals(attributes, testAttributes);
//		assertEquals(attributesArray[0], attributes.get(CommonElement.ATTRIBUTE_KEY_GUID));
//		assertEquals(attributesArray[1], attributes.get(CommonElement.ATTRIBUTE_KEY_NAME));
//		assertEquals(attributesArray[2], attributes.get(CommonElement.ATTRIBUTE_KEY_DESCRIPTION));
	}
	
	public void testPropertiesSet() {
		assertEquals(propertySetShouldBeOfType, currentPropertyList.getType());
		assertEquals(propertySetShouldHaveName, currentPropertyList.getName());
		
		//this.testNVPlist();
		this.testMap();
	}
	private void testMap() {
		assertEquals(propertyMapBeingTested.size(), mapBasedTestData.size());
		assertEquals(propertyMapBeingTested, mapBasedTestData);
	}
	public void testNVPlist() {
		assertEquals(nvpListBeingTested.size(), mapBasedTestData.size());
		for(NameValuePair nvp : nvpListBeingTested) {
			assertEquals(nvp.value, mapBasedTestData.get(nvp.name));
		}
	}
	@Test
	public void testFurnishingPropertiesSets() {
		currentPropertyList = furnishing1.getProperties().get(0);
		propertyMapBeingTested = currentPropertyList.getElementMap();
		propertySetShouldBeOfType = null;
		propertySetShouldHaveName = "Pset_FurnitureTypeCommon";
		mapBasedTestData = new HashMap<String, String>();
		mapBasedTestData.put("NominalDepth", "IFCLENGTHMEASURE(0.6)");
		mapBasedTestData.put("NominalLength", "IFCLENGTHMEASURE(5.6)");
		mapBasedTestData.put("NominalHeight", "IFCLENGTHMEASURE(0.7)");
		this.testPropertiesSet();
		
		currentPropertyList = furnishing2.getProperties().get(0);
		propertyMapBeingTested = currentPropertyList.getElementMap();
		propertySetShouldBeOfType = null;
		propertySetShouldHaveName = "Pset_FurnitureTypeCommon";
		mapBasedTestData = new HashMap<String, String>();
		mapBasedTestData.put("NominalDepth", "IFCLENGTHMEASURE(0.6)");
		mapBasedTestData.put("NominalLength", "IFCLENGTHMEASURE(0.6)");
		mapBasedTestData.put("NominalHeight", "IFCLENGTHMEASURE(2.5)");
		this.testPropertiesSet();
		
		currentPropertyList = furnishing3.getProperties().get(0);
		propertyMapBeingTested = currentPropertyList.getElementMap();
		this.testPropertiesSet();
	}
	
	@Test
	public void testSpatialContainers() {
		//List<SpatialContainerItem> spatialContainer = furnishing1.getSpatialcontainer();
		Decomposition spatialDecompostion = furnishing1.getSpatialDecomposition();
		
		/*assertEquals(1, spatialContainer.size());
		assertEquals("0h$ksovXH3Jeg0w$H7s8af", spatialContainer.get(0).guid);
		assertEquals("ifcspace", spatialContainer.get(0).type);*/
		
		assertNotNull(spatialDecompostion.getSpaces());
		assertEquals(1, spatialDecompostion.getSpaces().size());
		assertEquals("0h$ksovXH3Jeg0w$H7s8af", spatialDecompostion.getSpaces().get(0));
		
		
		/*spatialContainer = furnishing2.getSpatialcontainer();
		assertEquals(1, spatialContainer.size());
		assertEquals("0h$ksovXH3Jeg0w$H7s8af", spatialContainer.get(0).guid);
		assertEquals("ifcspace", spatialContainer.get(0).type);*/
		
		spatialDecompostion = furnishing2.getSpatialDecomposition();
		assertNotNull(spatialDecompostion.getSpaces());
		assertEquals(1, spatialDecompostion.getSpaces().size());
		assertEquals("0h$ksovXH3Jeg0w$H7s8af", spatialDecompostion.getSpaces().get(0));
		
		/*spatialContainer = furnishing3.getSpatialcontainer();
		assertEquals(1, spatialContainer.size());
		assertEquals("0h$ksovXH3Jeg0w$H7s8af", spatialContainer.get(0).guid);
		assertEquals("ifcspace", spatialContainer.get(0).type);*/
		spatialDecompostion = furnishing3.getSpatialDecomposition();
		assertNotNull(spatialDecompostion.getSpaces());
		assertEquals(1, spatialDecompostion.getSpaces().size());
		assertEquals("0h$ksovXH3Jeg0w$H7s8af", spatialDecompostion.getSpaces().get(0));
	}
}
