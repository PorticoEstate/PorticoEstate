package no.bimconverter.ifc;

import static org.junit.Assert.*;

import jsdai.lang.SdaiException;
import org.junit.After;
import org.junit.Before;
import org.junit.Test;

public class RepositoriesImplTest {
	Repositories repo = null;
	String systemRepository = "SystemRepository";
	String testingRepository = "TestRepository";
	String nonExistingRepository = "dummmmmyRepoThatDoesNotExist";
	String testIfcFileName = "sample.ifc";
	String invalidIfcFileNameMixedCase = "fake1mixedcase.ifc";
	String invalidIfcFileNameUpperCase = "fake2uppercase.ifc";
	int numberOfIfcElements = 163;
	@Before
	public void setUp() throws Exception {
		repo = new RepositoriesImpl();
		
	}

	@After
	public void tearDown() throws Exception {
		repo = null;
	}

	@Test
	public void testRepositoriesImpl() {
		assertNotNull(repo);
	}
	
	@Test
	public void testGetNumberOfRepositories() {
		assertTrue(repo.getNumberOfRepositories() > 0);
	}
	
	@Test
	public void testGetRepositoryNames() {
		assertEquals(repo.getRepositoryNames().get(0), "SystemRepository");
	}

	@Test
	public void testGetRepositoryStatus() {
		assertTrue(repo.getRepositoryStatus().getRepositoryInfo().size() > 0);
		System.out.println(repo.getRepositoryStatus());
	}

	@Test
	public void testCheckIfRepoExists() throws SdaiException {
		assertTrue(repo.checkIfRepositoryExists(systemRepository));
	}

	
	@Test
	public void testAddIfc() {
		//String ifcFilename = Thread.currentThread().getContextClassLoader().getResource(testIfcFileName).toString();
		String ifcFilename = getClass().getResource( "/" +testIfcFileName ).toString();
		// OS specific!
		ifcFilename = ifcFilename.replace("file:/", "");
		if(repo.checkIfRepositoryExists(testingRepository)) {
			repo.deleteRepository(testingRepository);
		}
		assertTrue(repo.addRepository(testingRepository, ifcFilename));
	}
	
	@Test(expected=RepositoryExceptionUc.class)
	public void testAddIfcAgain() {
		String ifcFilename = Thread.currentThread().getContextClassLoader().getResource(testIfcFileName).toString();
		// OS specific!
		ifcFilename = ifcFilename.replace("file:/", "");
		assertTrue(repo.addRepository(testingRepository, ifcFilename));
	}

	
	@Test
	public void testDeleteRepository() {
		assertTrue(repo.deleteRepository(testingRepository));		
	}
	
	@Test(expected=RepositoryExceptionUc.class)
	public void testDeleteRepositoryFailure() {
		assertFalse(repo.deleteRepository(nonExistingRepository));
	}
	
	@Test
	public void testAddIvalidIfcMixedCase() {
		String ifcFilename = Thread.currentThread().getContextClassLoader().getResource(invalidIfcFileNameMixedCase).toString();
		
		// OS specific!
		ifcFilename = ifcFilename.replace("file:/", "");
		if(repo.checkIfRepositoryExists(testingRepository)) {
			repo.deleteRepository(testingRepository);
		}
		try {
			repo.addRepository(testingRepository, ifcFilename);
		}  catch (InvalidIfcFileException e) {
			assertTrue(true);
		}	catch (RepositoryExceptionUc e) {
			System.out.println(e);
			System.out.println("MSG:"+e.getMessage());
			System.out.println("Cause:"+e.getCause().getMessage());
		}
	}
	
	@Test
	public void testAddIvalidIfcUpperCase() {
		String ifcFilename = Thread.currentThread().getContextClassLoader().getResource(invalidIfcFileNameUpperCase).toString();
		
		// OS specific!
		ifcFilename = ifcFilename.replace("file:/", "");
		if(repo.checkIfRepositoryExists(testingRepository)) {
			repo.deleteRepository(testingRepository);
		}
		try {
			repo.addRepository(testingRepository, ifcFilename);
		} catch (InvalidIfcFileException e) {
			assertTrue(true);
		} catch (RepositoryExceptionUc e) {
			System.out.println(e);
			System.out.println("MSG:"+e.getMessage());
			System.out.println("Cause:"+e.getCause().getMessage());
			
		}
		
		
	}
	

}
