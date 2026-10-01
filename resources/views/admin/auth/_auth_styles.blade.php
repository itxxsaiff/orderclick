{{-- Shared card styles for the admin auth pages (login, forgot password). --}}
<style>
    .ocl-auth { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 28px 16px;
        background: radial-gradient(120% 120% at 90% -10%, rgba(31,157,85,.10), transparent 55%), #f5f7f4; }
    .ocl-auth__card { width: 100%; max-width: 440px; background: #fff; border: 1px solid #e5e9e2; border-radius: 18px;
        box-shadow: 0 24px 60px -32px rgba(20,32,24,.4); padding: 34px 32px; }
    .ocl-auth__title { font-size: 26px; font-weight: 700; color: #17201a; margin: 0; }
    .ocl-auth__sub { color: #6b7669; margin: 6px 0 26px; font-size: 14.5px; }
    .ocl-auth__sub a { color: #1f9d55; font-weight: 600; text-decoration: none; }
    .ocl-fld { margin-bottom: 16px; }
    .ocl-fld label { display: block; font-size: 13.5px; font-weight: 600; color: #39443c; margin-bottom: 6px; }
    .ocl-fld label .req { color: #d64545; }
    .ocl-inp { width: 100%; height: 48px; border: 1px solid #d9e0d4; border-radius: 10px; padding: 0 14px;
        font-size: 15px; color: #17201a; background: #fff; transition: .15s; }
    .ocl-inp:focus { outline: none; border-color: #1f9d55; box-shadow: 0 0 0 3px rgba(31,157,85,.12); }
    .ocl-pw { position: relative; }
    .ocl-pw .ocl-inp { padding-inline-end: 46px; }
    .ocl-eye { position: absolute; inset-inline-end: 2px; top: 0; height: 48px; width: 44px; border: 0;
        background: none; color: #8a978d; cursor: pointer; display: flex; align-items: center;
        justify-content: center; border-radius: 10px; }
    .ocl-eye:hover { color: #1f9d55; }
    .ocl-forgot { text-align: end; margin: -4px 0 18px; }
    .ocl-forgot a { font-size: 13px; font-weight: 600; color: #6b7669; text-decoration: none; }
    .ocl-forgot a:hover { color: #1f9d55; }
    .ocl-submit { width: 100%; height: 50px; border: 0; border-radius: 11px; background: #1f9d55; color: #fff;
        font-weight: 650; font-size: 15.5px; cursor: pointer; transition: .15s; }
    .ocl-submit:hover { background: #198a49; }
    .ocl-err { color: #d64545; font-size: 12.5px; margin-top: 5px; display: block; }
    .ocl-demo { margin-top: 22px; }
    .ocl-demo table { width: 100%; font-size: 12.5px; border-collapse: collapse; }
    .ocl-demo td { border: 1px solid #e5e9e2; padding: 6px 8px; }
    .ocl-demo .themes { display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px; margin-top: 10px; }
    .ocl-demo .themes a { font-size: 11px; text-align: center; padding: 7px 4px; border-radius: 8px; background: #17201a; color: #fff; text-decoration: none; }
    .ocl-auth__icon { width: 52px; height: 52px; border-radius: 14px; background: rgba(31,157,85,.10); color: #1f9d55;
        display: flex; align-items: center; justify-content: center; font-size: 22px; margin-bottom: 18px; }
    .ocl-auth__back { display: block; text-align: center; margin-top: 20px; font-size: 13.5px; font-weight: 600;
        color: #6b7669; text-decoration: none; }
    .ocl-auth__back:hover { color: #1f9d55; }
    [dir="rtl"] .ocl-auth__back i { transform: scaleX(-1); }
</style>
