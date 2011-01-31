package no.bimconverter.ifc.v2x3;


import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertNotNull;
import static org.junit.Assert.assertTrue;

import java.util.Map;

import no.bimconverter.ifc.Repositories;
import no.bimconverter.ifc.RepositoriesImpl;

import org.junit.After;
import org.junit.Before;
import org.junit.Test;

public class IfcModelReportTest {

	String testingRepository = "ModelTestRepository";
	String nonExistingRepository = "dummmmmyRepoThatDoesNotExist";
	String testIfcFileName = "sample.ifc";
	int numberOfIfcElements = 163;
	int numberOfIfcObjects = 7;
	int numberOfIfcRelations = 12;
	String ifcFilename = null;
	Repositories repo = null;
	private int numberOfIfcResourceSchemas = 139;
	private int numberOfIfPropertySets = 5;
	
	@Before
	public void setUp() {
		ifcFilename = (Thread.currentThread().getContextClassLoader().getResource(testIfcFileName)).toString();
		ifcFilename = ifcFilename.replace("file:/", "");
		repo = new RepositoriesImpl();
		repo.addRepository(testingRepository, ifcFilename);
	}
	@After
	public void tearDown() {
		repo.deleteRepository(testingRepository);
	}
	
	@Test
	public void testCreateIfcModelReport() {
		IfcModelReport imr = new IfcModelReport();
		assertTrue(imr != null);
	}
	
	@Test
	public void testGetCurrentModelReport() {
		IfcModelReport imr = new IfcModelReport();
		Map<String, String> report = imr.getIfcModelReport();
		assertNotNull(report);
		
	}
	
	@Test
	public void testGetCorrectModelSize() {
		IfcModelReport imr = new IfcModelReport(testingRepository);
		Map<String, String> report = imr.getIfcModelReport();
		System.out.println(report);
		assertEquals(report.get("Model size"), String.valueOf(numberOfIfcElements));
	}
	@Test
	public void testGetNumberOfObjects() {
		IfcModelReport imr = new IfcModelReport(testingRepository);
		Map<String, String> report = imr.getIfcModelReport();
		assertEquals(report.get("Object count"), String.valueOf(numberOfIfcObjects));
	}
	@Test
	public void testGetNumberOfRelations() {
		IfcModelReport imr = new IfcModelReport(testingRepository);
		Map<String, String> report = imr.getIfcModelReport();
		assertEquals(report.get("Relation count"), String.valueOf(numberOfIfcRelations));
	}
	@Test
	public void testGetNumberOfResourceSchemas() {
		IfcModelReport imr = new IfcModelReport(testingRepository);
		Map<String, String> report = imr.getIfcModelReport();
		assertEquals(report.get("Resource schema count"), String.valueOf(numberOfIfcResourceSchemas ));
	}
	@Test
	public void testGetNumberOfPropertySets() {
		IfcModelReport imr = new IfcModelReport(testingRepository);
		Map<String, String> report = imr.getIfcModelReport();
		assertEquals(report.get("Property set count"), String.valueOf(numberOfIfPropertySets ));
	}

}
