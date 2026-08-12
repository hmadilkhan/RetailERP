<script>
    const roleSelect = document.getElementById('role');
    const companySelect = document.getElementById('company');
    const singleBranch = document.getElementById('singleBranch');
    const multipleBranch = document.getElementById('multipleBranch');
    const branchSelect = document.getElementById('branch');
    const multiBranchSelect = document.getElementById('multiplebranches');

    document.getElementById('vdimg')?.addEventListener('change', function () {
        if (this.files && this.files[0]) {
            document.getElementById('vdpimg').src = URL.createObjectURL(this.files[0]);
        }
    });

    companySelect?.addEventListener('change', function () {
        loadBranches(isManagerRole() ? multiBranchSelect : branchSelect, []);
    });

    roleSelect?.addEventListener('change', function () {
        // Options replace hone se pehle current selection yaad rakhein
        const keepBranchIds = selectedBranchIds();
        updateBranchMode();
        loadBranches(isManagerRole() ? multiBranchSelect : branchSelect, keepBranchIds);
    });

    updateBranchMode();

    document.getElementById('username')?.addEventListener('change', function () {
        if (!this.value.trim()) {
            return;
        }

        fetch("{{ url('/chk-user') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            body: JSON.stringify({ username: this.value })
        })
            .then(response => response.text())
            .then(response => {
                if (response.trim() === '1') {
                    alert('Username already exists!');
                    this.value = '';
                    this.focus();
                }
            });
    });

    document.getElementById('toggleRolePanel')?.addEventListener('click', function () {
        document.getElementById('rolePanel')?.classList.toggle('hidden');
    });

    document.getElementById('btnAddRole')?.addEventListener('click', function () {
        const roleName = document.getElementById('rolename').value.trim();
        if (!roleName) {
            alert('Required field can not be blank!');
            return;
        }

        fetch("{{ url('/add-role') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            body: JSON.stringify({ rolename: roleName })
        })
            .then(response => response.json())
            .then(response => {
                if (response === 2) {
                    alert('Particular Role Already Exist!');
                    return;
                }

                roleSelect.innerHTML = '<option value="">Select Role</option>';
                response.forEach(function (role) {
                    roleSelect.insertAdjacentHTML('beforeend', `<option value="${role.role_id}">${role.role}</option>`);
                });
                document.getElementById('rolename').value = '';
                document.getElementById('rolePanel')?.classList.add('hidden');
                alert('Role Added Successfully!');
            });
    });

    function isManagerRole() {
        return roleSelect?.value === '16' || roleSelect?.value === '18';
    }

    function updateBranchMode() {
        if (isManagerRole()) {
            multipleBranch?.classList.remove('hidden');
            singleBranch?.classList.add('hidden');
            if (multiBranchSelect) multiBranchSelect.disabled = false;
            if (branchSelect) branchSelect.disabled = true;
        } else {
            multipleBranch?.classList.add('hidden');
            singleBranch?.classList.remove('hidden');
            if (multiBranchSelect) multiBranchSelect.disabled = true;
            if (branchSelect) branchSelect.disabled = false;
        }
    }

    function selectedBranchIds() {
        const ids = [];
        if (branchSelect?.value) {
            ids.push(branchSelect.value);
        }
        if (multiBranchSelect) {
            Array.from(multiBranchSelect.selectedOptions).forEach(option => ids.push(option.value));
        }
        return ids.filter(Boolean);
    }

    function loadBranches(target, keepBranchIds) {
        if (!companySelect?.value || !target) {
            return;
        }

        const keep = keepBranchIds ?? selectedBranchIds();

        fetch("{{ url('/get-branches-by-company') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            body: JSON.stringify({ company: companySelect.value })
        })
            .then(response => response.json())
            .then(branches => {
                target.innerHTML = target.multiple ? '' : '<option value="">Select Branch</option>';
                branches.forEach(function (branch) {
                    target.insertAdjacentHTML('beforeend', `<option value="${branch.branch_id}">${branch.branch_name}</option>`);
                });

                // Pehle se selected branch dobara select karein warna khali post ho kar 0 lag jata hai
                keep.forEach(function (id) {
                    const option = target.querySelector(`option[value="${id}"]`);
                    if (option) {
                        option.selected = true;
                    }
                });
            });
    }
</script>
