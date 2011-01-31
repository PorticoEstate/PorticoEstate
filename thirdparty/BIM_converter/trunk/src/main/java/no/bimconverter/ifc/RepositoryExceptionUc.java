package no.bimconverter.ifc;
/*
 * Uc = Unchecked... should probably change this
 */
public class RepositoryExceptionUc extends RuntimeException {

	/**
	 * 
	 */
	private static final long serialVersionUID = -7670769841469006165L;
	
	public RepositoryExceptionUc() {
		super();
	}
	public RepositoryExceptionUc(String exception) {
		super(exception);
	}
	public RepositoryExceptionUc(String exception, Throwable t) {
		super(exception, t);
	}
	
}
