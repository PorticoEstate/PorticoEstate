package no.bimfm.ifc.v2x3.object.element;

import static junit.framework.Assert.assertNotNull;
import static org.junit.Assert.*;

import java.util.HashMap;
import java.util.List;
import java.util.Map;

import javax.xml.bind.JAXBContext;
import javax.xml.bind.JAXBException;
import javax.xml.bind.Marshaller;

import no.bimfm.ifc.Repositories;
import no.bimfm.ifc.RepositoriesImpl;
import no.bimfm.ifc.v2x3.IfcModelImpl;
import no.bimfm.jaxb.Attributes;
import no.bimfm.jaxb.BaseQuantities;
import no.bimfm.jaxb.Decomposition;
import no.bimfm.jaxb.MaterialItem;
import no.bimfm.jaxb.NameValuePair;
import no.bimfm.jaxb.PropertyList;
import no.bimfm.jaxb.SpatialContainerItem;

import org.junit.After;
import org.junit.Before;
import org.junit.Test;


public class CoveringTest {
	//String testingRepository = "ModelTestRepository";
	String testingRepository = "FMHandoverRepository";
	String nonExistingRepository = "dummmmmyRepoThatDoesNotExist";
	//String testIfcFileName = "sample.ifc";
	String testIfcFileName = "20091007_Test_BasicFM-HandOver_01_valid.ifc";
	
	int numberOfIfcElements = 1420;
	int numberOfIfcModels = 0;
	String ifcFilename = null;
	private IfcModelImpl model;
	private Covering covering1;
	private Covering covering2;
	private Covering covering3;
	Repositories repo = null;
	private List<Covering> coverings;
	private Map<String,String> baseQuantitiesTestData;
	private Map<String,String> baseQuantitiesCurrentData;
	
	
	@Before
	public void setUp() {
		model = new IfcModelImpl(testingRepository);
		ifcFilename = (Thread.currentThread().getContextClassLoader().getResource(testIfcFileName)).toString();
		ifcFilename = ifcFilename.replace("file:/", "");
		repo = new RepositoriesImpl();
		repo.addRepository(testingRepository, ifcFilename);
		coverings = model.getCoverings();
		
		covering1 = coverings.get(0);
		covering2 = coverings.get(1);
		covering3 = coverings.get(2);
	}
	@After
	public void tearDown() {
		repo.deleteRepository(testingRepository);
	}
	
	@Test
	public void testCoveringObject() throws JAXBException {
		assertEquals(3, coverings.size()); 
		assertNotNull(coverings);
	}
	@Test
	public void testDisplayCovering1() throws JAXBException {
		assertNotNull(covering1);
		JAXBContext jc = JAXBContext.newInstance(Covering.class);
		Marshaller m = jc.createMarshaller();
		m.setProperty(Marshaller.JAXB_FORMATTED_OUTPUT, true);
		m.marshal(covering1, System.out );
	}
	@Test
	public void testCovering1Attributes() {
		Attributes attributes = covering1.getAttributes();
		Attributes testAttributes = new Attributes();
		testAttributes.setGuid("0h$ksovXH3Jeg02dHdf8af");
		testAttributes.setName("FB-1:EG.004");
		testAttributes.setDescription("Flooring-001 object for testing Basic FM Handover");
		testAttributes.setPredefinedCoveringType("FLOORING");
		assertEquals(attributes, testAttributes);
	}
	@Test
	public void testCovering1BaseQuantities() {
		//List<NameValuePair> basequantities = covering1.getBaseQuantities();
		this.baseQuantitiesCurrentData = covering1.getBaseQuantities().getElementMap();
		this.baseQuantitiesTestData = new HashMap<String, String>();
		this.baseQuantitiesTestData.put("NetArea", "16.66");
		this.baseQuantitiesTestData.put("GrossArea", "16.66");
		this.compareBaseQuantities();
		/*assertEquals(2, basequantities.size());
		int testCount = 0;
		for(NameValuePair item : basequantities) {
			if(item.name.equals("NetArea")) {
				assertEquals("16.66", item.value);
				testCount++;
			} else if(item.name.equals("GrossArea")) {
				assertEquals("16.66", item.value);
				testCount++;
			}
		}
		assertEquals(2, testCount);*/
	}
	
	private void compareBaseQuantities() {
		assertEquals(this.baseQuantitiesCurrentData, this.baseQuantitiesTestData);
	}
	@Test
	public void testCovering1Properties() {
		List<PropertyList> spaceProperties = covering1.getProperties();
		assertEquals(1, spaceProperties.size());
		PropertyList pList = spaceProperties.get(0);
		assertEquals(Covering.commonPropertyName, pList.getName());
		Map<String, String> propertyMap = pList.getElementMap();
		Map<String, String> currentProperties = new HashMap<String, String>();
		currentProperties.put("Reference", "IFCLABEL('FB-1')");
		assertEquals(currentProperties.size(), propertyMap.size());
		assertEquals(currentProperties, propertyMap);
		/*int testCount = 0;
		for(NameValuePair nvp : pList.getProperties()) {
			if(nvp.name.equals("Reference")) {
				assertEquals("IFCLABEL('FB-1')", nvp.value);
				testCount++;
			}
		}
		assertEquals(testCount, 1);*/
	}
	@Test
	public void testCovering1Materials() {
		List<MaterialItem> materials = covering1.getMaterial();
		assertEquals(1, materials.size());
		MaterialItem item1 = materials.get(0);
		assertEquals("layerSet", item1.getType());
		assertEquals("Slate on flooting floor", item1.getLayerSetName());
		assertEquals("Slate", item1.getMaterialLayer().get(0).name);
		assertEquals("0.03", item1.getMaterialLayer().get(0).thickness);
		assertEquals("Flooting floor", item1.getMaterialLayer().get(1).name);
		assertEquals("0.07", item1.getMaterialLayer().get(1).thickness);
	}
	@Test
	public void testSpatialContainer1() {
		/*List<SpatialContainerItem> spatialContainer = covering1.getSpatialcontainer();
		assertEquals(1, spatialContainer.size());
		assertEquals("0h$ksovXH3Jeg02dH7s8af", spatialContainer.get(0).guid);*/
		
		Decomposition spatialDecompostion = covering1.getSpatialDecomposition();
		assertEquals(1, spatialDecompostion.getSpaces().size());
		assertEquals("0h$ksovXH3Jeg02dH7s8af", spatialDecompostion.getSpaces().get(0));
	}
	@Test
	public void testDisplayCovering2() throws JAXBException {
		assertNotNull(covering2);
		JAXBContext jc = JAXBContext.newInstance(Covering.class);
		Marshaller m = jc.createMarshaller();
		m.setProperty(Marshaller.JAXB_FORMATTED_OUTPUT, true);
		m.marshal(covering2, System.out );
	}
	@Test
	public void testCovering2Attributes() {
		/*Map<String, String> attributes = covering2.getAttributes();
		assertEquals("0h$ksovXH3Jegs2dHdf8af", attributes.get(CommonElement.ATTRIBUTE_KEY_GUID));
		assertEquals("UD-1:EG.004", attributes.get(CommonElement.ATTRIBUTE_KEY_NAME));
		assertEquals("Ceiling-002 object for testing Basic FM Handover", attributes.get(CommonElement.ATTRIBUTE_KEY_DESCRIPTION));
		assertEquals("CEILING", attributes.get(Covering.Attribute.PREDEFINED_COVERING_TYPE.getKey()));
		*/
		Attributes attributes = covering2.getAttributes();
		Attributes testAttributes = new Attributes();
		testAttributes.setGuid("0h$ksovXH3Jegs2dHdf8af");
		testAttributes.setName("UD-1:EG.004");
		testAttributes.setDescription("Ceiling-002 object for testing Basic FM Handover");
		testAttributes.setPredefinedCoveringType("CEILING");
		assertEquals(attributes, testAttributes);
	}
	@Test
	public void testCovering2BaseQuantities() {
		this.baseQuantitiesCurrentData = covering2.getBaseQuantities().getElementMap();
		this.baseQuantitiesTestData = new HashMap<String, String>();
		this.baseQuantitiesTestData.put("NetArea", "25.16");
		this.baseQuantitiesTestData.put("GrossArea", "25.16");
		this.compareBaseQuantities();
		
		/*List<NameValuePair> basequantities = covering2.getBaseQuantities();
		assertEquals(2, basequantities.size());
		int testCount = 0;
		for(NameValuePair item : basequantities) {
			if(item.name.equals("NetArea")) {
				assertEquals("25.16", item.value);
				testCount++;
			} else if(item.name.equals("GrossArea")) {
				assertEquals("25.16", item.value);
				testCount++;
			}
		}
		assertEquals(2, testCount);*/
	}
	@Test
	public void testCovering2Properties() {
		List<PropertyList> spaceProperties = covering2.getProperties();
		assertEquals(1, spaceProperties.size());
		PropertyList pList = spaceProperties.get(0);
		assertEquals(Covering.commonPropertyName, pList.getName());
		Map<String, String> propertyMap = pList.getElementMap();
		Map<String, String> currentProperties = new HashMap<String, String>();
		currentProperties.put("Reference", "IFCLABEL('UD-1')");
		assertEquals(currentProperties.size(), propertyMap.size());
		assertEquals(currentProperties, propertyMap);
		
		/*int testCount = 0;
		for(NameValuePair nvp : pList.getProperties()) {
			if(nvp.name.equals("Reference")) {
				assertEquals("IFCLABEL('UD-1')", nvp.value);
				testCount++;
			}
		}
		assertEquals(testCount, 1);*/
	}
	@Test
	public void testCovering2Materials() {
		List<MaterialItem> materials = covering2.getMaterial();
		assertEquals(1, materials.size());
		MaterialItem item1 = materials.get(0);
		assertEquals("layerSet", item1.getType());
		assertEquals("Suspended ceiling gypsum panels", item1.getLayerSetName());
		assertEquals("Slate", item1.getMaterialLayer().get(0).name);
		assertEquals("0.1", item1.getMaterialLayer().get(0).thickness);
		
	}
	@Test
	public void testSpatialContainer2() {
		/*List<SpatialContainerItem> spatialContainer = covering2.getSpatialcontainer();
		assertEquals(1, spatialContainer.size());
		assertEquals("0h$ksovXH3Jeg0w$H7s8af", spatialContainer.get(0).guid);*/
		
		Decomposition spatialDecompostion = covering2.getSpatialDecomposition();
		assertEquals(1, spatialDecompostion.getSpaces().size());
		assertEquals("0h$ksovXH3Jeg0w$H7s8af", spatialDecompostion.getSpaces().get(0));
	}
	@Test
	public void testDisplayCovering3() throws JAXBException {
		assertNotNull(covering3);
		JAXBContext jc = JAXBContext.newInstance(Covering.class);
		Marshaller m = jc.createMarshaller();
		m.setProperty(Marshaller.JAXB_FORMATTED_OUTPUT, true);
		m.marshal(covering3, System.out );
	}
	@Test
	public void testCovering3Attributes() {
		/*Map<String, String> attributes = covering3.getAttributes();
		assertEquals("0h$ksovXH3Jegsd3Hdf8af", attributes.get(CommonElement.ATTRIBUTE_KEY_GUID));
		assertEquals("UD-1a:EG.004", attributes.get(CommonElement.ATTRIBUTE_KEY_NAME));
		assertEquals("Ceiling-003 object for testing Basic FM Handover", attributes.get(CommonElement.ATTRIBUTE_KEY_DESCRIPTION));
		assertEquals("CEILING", attributes.get(Covering.Attribute.PREDEFINED_COVERING_TYPE.getKey()));*/
		Attributes attributes = covering3.getAttributes();
		Attributes testAttributes = new Attributes();
		testAttributes.setGuid("0h$ksovXH3Jegsd3Hdf8af");
		testAttributes.setName("UD-1a:EG.004");
		testAttributes.setDescription("Ceiling-003 object for testing Basic FM Handover");
		testAttributes.setPredefinedCoveringType("CEILING");
		assertEquals(attributes, testAttributes);
	}
	@Test
	public void testCovering3BaseQuantities() {
		this.baseQuantitiesCurrentData = covering3.getBaseQuantities().getElementMap();
		this.baseQuantitiesTestData = new HashMap<String, String>();
		this.baseQuantitiesTestData.put("NetArea", "2.72");
		this.baseQuantitiesTestData.put("GrossArea", "2.72");
		this.compareBaseQuantities();
		
		/*List<NameValuePair> basequantities = covering3.getBaseQuantities();
		assertEquals(2, basequantities.size());
		int testCount = 0;
		for(NameValuePair item : basequantities) {
			if(item.name.equals("NetArea")) {
				assertEquals("2.72", item.value);
				testCount++;
			} else if(item.name.equals("GrossArea")) {
				assertEquals("2.72", item.value);
				testCount++;
			}
		}
		assertEquals(2, testCount);*/
	}
	@Test
	public void testCovering3Properties() {
		List<PropertyList> spaceProperties = covering3.getProperties();
		assertEquals(1, spaceProperties.size());
		PropertyList pList = spaceProperties.get(0);
		assertEquals(Covering.commonPropertyName, pList.getName());
		Map<String, String> propertyMap = pList.getElementMap();
		Map<String, String> currentProperties = new HashMap<String, String>();
		currentProperties.put("Reference", "IFCLABEL('UD-1')");
		assertEquals(currentProperties.size(), propertyMap.size());
		assertEquals(currentProperties, propertyMap);
		
		/*int testCount = 0;
		for(NameValuePair nvp : pList.getProperties()) {
			if(nvp.name.equals("Reference")) {
				assertEquals("IFCLABEL('UD-1')", nvp.value);
				testCount++;
			}
		}
		assertEquals(testCount, 1);*/
	}
	@Test
	public void testCovering3Materials() {
		List<MaterialItem> materials = covering3.getMaterial();
		assertEquals(1, materials.size());
		MaterialItem item1 = materials.get(0);
		assertEquals("layerSet", item1.getType());
		assertEquals("Suspended ceiling gypsum panels", item1.getLayerSetName());
		assertEquals("Slate", item1.getMaterialLayer().get(0).name);
		assertEquals("0.1", item1.getMaterialLayer().get(0).thickness);
		
	}
	@Test
	public void testSpatialContainer3() {
		/*List<SpatialContainerItem> spatialContainer = covering3.getSpatialcontainer();
		assertEquals(1, spatialContainer.size());
		assertEquals("0h$ksovXH3Jeg0w$H7s8af", spatialContainer.get(0).guid);*/
		
		Decomposition spatialDecompostion = covering3.getSpatialDecomposition();
		assertEquals(1, spatialDecompostion.getSpaces().size());
		assertEquals("0h$ksovXH3Jeg0w$H7s8af", spatialDecompostion.getSpaces().get(0));
	}
}
