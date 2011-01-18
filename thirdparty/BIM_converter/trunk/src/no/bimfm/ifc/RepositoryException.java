package no.bimfm.ifc;

public class RepositoryException extends Exception {

	/**
	 * 
	 */
	private static final long serialVersionUID = -7670769841469006165L;
	
	public RepositoryException() {
		super();
	}
	public RepositoryException(String exception) {
		super(exception);
	}
	public RepositoryException(String exception, Throwable t) {
		super(exception, t);
	}
	
}
