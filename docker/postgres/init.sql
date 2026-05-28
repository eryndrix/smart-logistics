DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_database WHERE datname = 'smart_logistics_test'
    ) THEN
        CREATE DATABASE smart_logistics_test;
    END IF;
END $$;
